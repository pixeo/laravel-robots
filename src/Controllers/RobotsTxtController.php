<?php
namespace Pixeo\RobotsTxt\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class RobotsTxtController extends Controller
{
    /**
     * By default, the production environment allows all, and every other environment allows none
     * Custom paths can be set by publishing and editing the config file
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $envs = config('robots-txt.paths');
        $env = config('app.env');

        // if no env is set, or one of the set envs cannot be
        // matched against the current env, use the default
        if ($envs === null || !array_key_exists($env, $envs)) {
            return $this->response($this->defaultRobot());
        }

        // Get sitemap
        $agents = $envs[$env];

        $sitemap = Arr::get($agents, 'sitemap');

        unset($agents['sitemap']);

        // for each user agent, get the user agent name and the paths
        // for the agent, appending them to the result string
        $robots = collect($agents)
            ->map(function ($paths, $name) {
                $robot = 'User-agent: ' . $name . PHP_EOL;

                foreach ($paths as $path) {
                    $robot .= 'Disallow: ' . $path . PHP_EOL;
                }

                return $robot;
            });

        if ($sitemap) {
            $robots->push("Sitemap: {$sitemap}" . PHP_EOL);
        }

        // output the entire robots.txt
        return $this->response($robots->implode(PHP_EOL));
    }

    /**
     * Default 'Disallow /' for every robot
     *
     * @return string user agent and disallow string
     */
    protected function defaultRobot()
    {
        return 'User-agent: *' . PHP_EOL . 'Disallow: /';
    }

    /**
     * @param  string $robots
     * @return \Illuminate\Http\Response
     */
    protected function response(string $robots)
    {
        return response()->make($robots, 200, array('content-type' => 'text/plain'));
    }
}
