<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    // Admins see clinic analytics; patients see their own summary
    public function index(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return $this->adminDashboard($request);
        }

        return $this->patientDashboard($request);
    }

    private function adminDashboard(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $today = now()->toDateString();

        // All appointments inside the chosen date range
        $appointments = Appointment::where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->get();

        $completed = $appointments->where('status', 'completed');

        // KPI summary cards
        $stats = [
            'today' => Appointment::where('date', $today)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->count(),
            'pending_all' => Appointment::where('status', 'pending')->count(),
            'total' => $appointments->count(),
            'completed' => $completed->count(),
            'cancelled' => $appointments->whereIn('status', ['cancelled', 'no_show'])->count(),
            'revenue' => $completed->sum('service_price'),
        ];
        $stats['completion_rate'] = $stats['total'] > 0
            ? round($stats['completed'] / $stats['total'] * 100)
            : 0;

        // Chart 1: appointments per day (every day in the range, including days with 0)
        $perDay = $appointments->countBy('date');
        $dailyLabels = [];
        $dailyCounts = [];
        foreach (CarbonPeriod::create($from, $to) as $day) {
            $dailyLabels[] = $day->format('M j');
            $dailyCounts[] = $perDay[$day->toDateString()] ?? 0;
        }

        // Chart 2: status breakdown
        $byStatus = collect(Appointment::STATUSES)
            ->map(fn ($label, $code) => $appointments->where('status', $code)->count());

        // Chart 3 and 4: top services and appointments per dentist (cancelled ones not counted)
        $notCancelled = $appointments->where('status', '!=', 'cancelled');
        $byService = $notCancelled->countBy('service_name')->sortDesc()->take(5);
        $byDentist = $notCancelled->countBy('dentist_name')->sortDesc();

        // Next 5 upcoming appointments
        $upcoming = Appointment::where('date', '>=', $today)
            ->whereIn('status', Appointment::ACTIVE_STATUSES)
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'from', 'to', 'stats', 'dailyLabels', 'dailyCounts', 'byStatus', 'byService', 'byDentist', 'upcoming'
        ));
    }

    private function patientDashboard(Request $request)
    {
        $mine = Appointment::where('patient_id', (string) $request->user()->getKey())->get();

        $upcoming = $mine
            ->filter(fn ($a) => in_array($a->status, Appointment::ACTIVE_STATUSES) && $a->isUpcoming())
            ->sortBy(fn ($a) => $a->date . ' ' . $a->time)
            ->values();

        $stats = [
            'upcoming' => $upcoming->count(),
            'completed' => $mine->where('status', 'completed')->count(),
            'cancelled' => $mine->whereIn('status', ['cancelled', 'no_show'])->count(),
        ];
        $next = $upcoming->first();

        return view('dashboard-patient', compact('stats', 'next'));
    }

    // REPORT: download the appointments in the date range as a CSV file (opens in Excel)
    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->dateRange($request);

        $appointments = Appointment::where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return response()->streamDownload(function () use ($appointments) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");   // lets Excel read special letters like ñ correctly

            fputcsv($out, ['Date', 'Start', 'End', 'Patient', 'Service', 'Dentist', 'Price (PHP)', 'Status', 'Notes']);

            foreach ($appointments as $a) {
                $start = strtotime("{$a->date} {$a->time}");
                fputcsv($out, [
                    $a->date,
                    date('H:i', $start),
                    date('H:i', $start + $a->duration_minutes * 60),
                    $this->csvSafe($a->patient_name),
                    $this->csvSafe($a->service_name),
                    $this->csvSafe($a->dentist_name),
                    number_format($a->service_price, 2, '.', ''),
                    $a->statusLabel(),
                    $this->csvSafe($a->notes),
                ]);
            }

            fclose($out);
        }, "smilecare-appointments-{$from}-to-{$to}.csv", ['Content-Type' => 'text/csv']);
    }

    // Date range from the filter form (default: this month, maximum 92 days)
    private function dateRange(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'required_with:to', 'date_format:Y-m-d'],
            'to' => ['nullable', 'required_with:from', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $from = $validated['from'] ?? now()->startOfMonth()->toDateString();
        $to = $validated['to'] ?? now()->endOfMonth()->toDateString();

        if (Carbon::parse($from)->diffInDays(Carbon::parse($to)) > 92) {
            $to = Carbon::parse($from)->addDays(92)->toDateString();
        }

        return [$from, $to];
    }

    // SECURITY: stops "CSV injection" (text starting with = + - @ being run as a formula in Excel)
    private function csvSafe($value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }
}