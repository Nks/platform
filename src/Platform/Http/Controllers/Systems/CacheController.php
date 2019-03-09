<?php

declare(strict_types=1);

namespace Orchid\Platform\Http\Controllers\Systems;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Orchid\Platform\Http\Controllers\Controller;
use Orchid\Support\Facades\Alert;

class CacheController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $action = $request->get('action', 'index');

        try {
            $this->$action();
            Alert::success(__('Operation completed successfully.'));
        } catch (\Exception $exception) {
            Alert::warning($exception->getMessage());
        }

        Alert::success(__('Operation completed successfully.'));

        return response()->redirectToRoute('platform.systems.index');
    }

    /**
     *  Flush the application cache.
     */
    protected function cache()
    {
        Artisan::call('cache:clear');
    }

    /**
     * Create a cache file for faster configuration loading.
     */
    protected function config()
    {
        Artisan::call('config:cache');
    }

    /**
     * Create a route cache file for faster route registration.
     */
    protected function route()
    {
        Artisan::call('route:cache');
    }

    /**
     * Clear all compiled view files.
     */
    protected function view()
    {
        Artisan::call('view:clear');
    }
}
