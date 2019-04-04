<?php

namespace Pixeo\RobotsTxt\Tests;

class RobotsTxtTest extends IntegrationTest
{
    /** @test */
    public function it_returns_default_response_for_production_env()
    {
        config(['app.env' => 'production']);

        $this->get('/robots.txt')
            ->assertSee('User-agent: *')
            ->assertSee('Disallow: ')
            ->assertDontSee('Disallow: /'  . PHP_EOL);
    }

    /** @test */
    public function it_returns_sitemap_for_production_env()
    {
        config([
            'app.env' => 'production',
            'robots-txt.paths.production' => [
                '*' => ['/foobar'],
                'sitemap' => 'http://localhost/sitemap.xml',
            ],
        ]);

        $this->withoutExceptionHandling();

        $this->get('/robots.txt')
            ->assertSee('User-agent: *')
            ->assertSee('Sitemap: http://localhost/sitemap.xml')
            ->assertSee('Disallow: ')
            ->assertDontSee('Disallow: /'  . PHP_EOL);
    }

    /** @test */
    public function it_returns_default_response_for_non_production_env()
    {
        config(['app.env' => 'staging']);

        $response = $this->get('/robots.txt');

        $response->assertSee('User-agent: *' . PHP_EOL . 'Disallow: /');
        $response->assertDontSee('Disallow:  ');
    }

    /** @test */
    public function it_show_custom_set_paths()
    {
        config([
            'app.env' => 'production',
            'robots-txt.paths' => [
                'production' => [
                    '*' => ['/foobar']
                ]
            ]
        ]);

        $response = $this->get('/robots.txt');

        $response->assertSee('User-agent: *'. PHP_EOL . 'Disallow: /foobar');
        $response->assertDontSee('Disallow:  ');
    }

    /** @test */
    public function it_shows_multiple_user_agents()
    {
        $paths = [
            'production' => [
                'bot1' => [],
                'bot2' => []
            ]
        ];

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $response = $this->get('/robots.txt');

        $response->assertSee('User-agent: bot1' . PHP_EOL);
        $response->assertSee('User-agent: bot2' . PHP_EOL);
        $response->assertDontSee('Disallow:  ');
        $response->assertDontSee('Disallow: /' . PHP_EOL);
    }

    /** @test */
    public function it_shows_multiple_paths_per_agent()
    {
        $paths = [
            'production' => [
                '*' => [
                    '/foobar',
                    '/barfoo'
                ],
            ]
        ];

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $response = $this->get('/robots.txt');

        $response->assertSee('User-agent: *' . PHP_EOL . 'Disallow: /foobar' . PHP_EOL . 'Disallow: /barfoo');
        $response->assertDontSee('Disallow:  ');
        $response->assertDontSee('Disallow: /' . PHP_EOL);
    }

    /** @test */
    public function it_shows_multiple_paths_for_multiple_agents()
    {
        $paths = [
            'production' => [
                '*' => [
                    '/foobar',
                    '/barfoo'
                ],
                'bot1' => [
                    '/helloworld',
                    '/sorryicantdothatdave'
                ]
            ]
        ];

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $response = $this->get('/robots.txt');

        $response->assertSee('User-agent: *' . PHP_EOL . 'Disallow: /foobar' . PHP_EOL . 'Disallow: /barfoo');
        $response->assertSee('User-agent: bot1' . PHP_EOL . 'Disallow: /helloworld' . PHP_EOL . 'Disallow: /sorryicantdothatdave');
        $response->assertDontSee('Disallow:  ');
        $response->assertDontSee('Disallow: /' . PHP_EOL);
    }

    /** @test */
    public function it_shows_correct_paths_for_multiple_environments()
    {
        $paths = [
            'production' => [
                '*' => [
                    '/foobar',
                ]
            ],
            'staging' => [
                '*' => [
                    '/barfoo'
                ]
            ]
        ];

        config([
            'app.env' => 'production',
            'robots-txt.paths' => $paths
        ]);

        $response = $this->get('/robots.txt');

        $response->assertSee('User-agent: *' . PHP_EOL . 'Disallow: /foobar');
        $response->assertDontSee('User-agent: *' . PHP_EOL . 'Disallow: /barfoo');
        $response->assertDontSee('Disallow:  ');
        $response->assertDontSee('Disallow: /' . PHP_EOL);

        config(
            [
            'app.env' => 'staging',
            'robots-txt.paths' => $paths
            ]
        );

        $response = $this->get('/robots.txt');

        $response->assertSee('User-agent: *' . PHP_EOL . 'Disallow: /barfoo');
        $response->assertDontSee('User-agent: *' . PHP_EOL . 'Disallow: /foobar');
        $response->assertDontSee('Disallow:  ');
        $response->assertDontSee('Disallow: /' . PHP_EOL);
    }
}