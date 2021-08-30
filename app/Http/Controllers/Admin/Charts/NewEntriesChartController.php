<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Booking;
use App\Models\CancelEventSub;
use App\Models\Ceremony;
use App\User;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Category;
use Backpack\NewsCRUD\app\Models\Tag;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class NewEntriesChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();

        // MANDATORY. Set the labels for the dataset points
        $labels = [];
        for ($days_backwards = 30; $days_backwards >= 0; $days_backwards--) {
            if ($days_backwards == 1) {
            }
            $labels[] = $days_backwards.' days ago';
        }
        $this->chart->labels($labels);

        // RECOMMENDED. Set URL that the ChartJS library should call, to get its data using AJAX.
        $this->chart->load(backpack_url('charts/new-entries'));

        // OPTIONAL
        $this->chart->minimalist(false);
        $this->chart->displayLegend(true);
    }

    /**
     * Respond to AJAX calls with all the chart data points.
     *
     * @return json
     */
    public function data()
    {
        for ($days_backwards = 30; $days_backwards >= 0; $days_backwards--) {
            // Could also be an array_push if using an array rather than a collection.
            $users[] = Ceremony::whereDate('created_at', today()->subDays($days_backwards))
                            ->count();
            $articles[] = Booking::whereDate('created_at', today()->subDays($days_backwards))
                            ->count();
            $categories[] = CancelEventSub::whereDate('created_at', today()->subDays($days_backwards))
                            ->count();

        }

        $this->chart->dataset('Events', 'line', $users)
            ->color('rgb(66, 186, 150)')
            ->backgroundColor('rgba(66, 186, 150, 0.4)');

        $this->chart->dataset('Bookings', 'line', $articles)
            ->color('rgb(96, 92, 168)')
            ->backgroundColor('rgba(96, 92, 168, 0.4)');

        $this->chart->dataset('Cancel Bookings', 'line', $categories)
            ->color('rgb(255, 193, 7)')
            ->backgroundColor('rgba(255, 193, 7, 0.4)');


    }
}
