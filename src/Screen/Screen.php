<?php

declare(strict_types=1);

namespace Orchid\Screen;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Orchid\Platform\Http\Controllers\Controller;

/**
 * Class Screen.
 */
abstract class Screen extends Controller
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name;

    /**
     * Display header description.
     *
     * @var string
     */
    public $description;

    /**
     * @var Request
     */
    public $request;

    /**
     * Permission.
     *
     * @var string
     */
    public $permission;

    /**
     * @var Repository
     */
    private $post;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Screen constructor.
     */
    public function __construct()
    {
        $this->request = request();
    }

    /**
     * Button commands.
     *
     * @return array
     */
    abstract public function commandBar(): array;

    /**
     * Views.
     *
     * @return array
     */
    abstract public function layout(): array;

    /**
     * @return \Illuminate\Contracts\View\View
     * @throws \Throwable
     */
    public function build(): View
    {
        $layout = Layouts::blank([
            $this->layout(),
        ]);

        return $layout->build($this->post);
    }

    /**
     * @param mixed $method
     * @param mixed $slugLayouts
     *
     * @return \Illuminate\Contracts\View\View
     * @throws \Throwable
     */
    public function asyncBuild($method, $slugLayouts)
    {
        $this->arguments = $this->request->json()->all();

        $this->reflectionParams($method);
        $query = call_user_func_array([$this, $method], $this->arguments);
        $post = new Repository($query);

        foreach ($this->layout() as $layout) {
            if (property_exists($layout, 'slug') && $layout->slug === $slugLayouts) {
                return $layout->build($post, true);
            }
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function view()
    {
        $this->reflectionParams('query');
        $query = call_user_func_array([$this, 'query'], $this->arguments);
        $this->post = new Repository($query);

        return view('platform::container.layouts.base', [
            'screen'    => $this,
        ]);
    }

    /**
     * @param array $parameters
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function handle(...$parameters)
    {
        abort_if(! $this->checkAccess(), 403);

        if ($this->request->method() === 'GET' || (! count($parameters))) {
            $this->arguments = $parameters;

            return $this->view();
        }

        $method = array_pop($parameters);
        $this->arguments = $parameters;

        if (starts_with($method, 'async')) {
            return $this->asyncBuild($method, array_pop($this->arguments));
        }

        $this->reflectionParams($method);

        return call_user_func_array([$this, $method], $this->arguments);
    }

    /**
     * @param $method
     *
     * @throws \ReflectionException
     */
    public function reflectionParams($method)
    {
        $class = new \ReflectionClass($this);

        if (! is_string($method)) {
            return;
        }

        if (! $class->hasMethod($method)) {
            return;
        }

        $parameters = $class->getMethod($method)->getParameters();

        $arguments = [];

        foreach ($parameters as $key => $parameter) {
            $arguments[] = $this->bind($key, $parameter);
        }
        $this->arguments = $arguments;
    }

    /**
     * @param $key
     * @param $parameter
     *
     * @return mixed
     */
    private function bind($key, $parameter)
    {
        if (is_null($parameter->getClass())) {
            return $this->arguments[$key] ?? null;
        }

        $class = $parameter->getClass()->name;

        $object = array_first($this->arguments, function ($value) use ($class) {
            return is_subclass_of($value, $class) || is_a($value, $class);
        });

        if (is_null($object)) {
            $object = app()->make($class);

            if (method_exists($object, 'resolveRouteBinding') && isset($this->arguments[$key])) {
                $object = $object->resolveRouteBinding($this->arguments[$key]);
            }
        }

        return $object;
    }

    /**
     * @return bool
     */
    private function checkAccess(): bool
    {
        if (is_null($this->permission)) {
            return true;
        }

        if (is_string($this->permission)) {
            $this->permission = [$this->permission];
        }

        foreach ($this->permission as $item) {
            if (Auth::user()->hasAccess($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function buildCommandBar() : array
    {
        $commands = [];
        foreach ($this->commandBar() as $command) {
            $commands[] = $command->build($this->post, $this->arguments);
        }

        return $commands;
    }
}
