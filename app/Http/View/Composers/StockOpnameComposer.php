<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\StockOpnameSession;

class StockOpnameComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $so_exist = StockOpnameSession::where('status', 'Open')->exists();

        if ($so_exist) {
            $view->with('so_exist', true);
        } else {
            $view->with('so_exist', false);
        }
    }
}