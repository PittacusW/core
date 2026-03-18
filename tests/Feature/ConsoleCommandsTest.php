<?php

namespace Pittacusw\Core\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Pittacusw\Core\Support\ProcessResult;
use Pittacusw\Core\Tests\TestCase;
use Pittacusw\Core\Commands\GitAddCommand;
use Pittacusw\Core\Contracts\RunsExternalProcesses;
use Pittacusw\Core\Tests\Support\FakeProcessRunner;

class ConsoleCommandsTest extends TestCase {

  public function test_git_add_passes_the_commit_message_as_a_single_argument()
  : void {
    $fakeRunner = new FakeProcessRunner([
      new ProcessResult(0),
      new ProcessResult(1),
      new ProcessResult(0),
      new ProcessResult(0),
    ]);

    $this->app->bind(RunsExternalProcesses::class, fn() => $fakeRunner);
    $this->app->forgetInstance(GitAddCommand::class);

    $exitCode = Artisan::call('git:add', [
      'message' => 'release"; rm -rf /',
    ]);

    $this->assertSame(0, $exitCode);
    $this->assertSame(
      ['git', 'commit', '-m', 'release"; rm -rf /'],
      $fakeRunner->commands[2],
    );
  }

  public function test_git_add_skips_commit_and_push_when_nothing_is_staged()
  : void {
    $fakeRunner = new FakeProcessRunner([
      new ProcessResult(0),
      new ProcessResult(0),
    ]);

    $this->app->bind(RunsExternalProcesses::class, fn() => $fakeRunner);
    $this->app->forgetInstance(GitAddCommand::class);

    $exitCode = Artisan::call('git:add');

    $this->assertSame(0, $exitCode);
    $this->assertSame([
      ['git', 'add', '.'],
      ['git', 'diff', '--cached', '--quiet'],
    ], $fakeRunner->commands);
  }

  public function test_git_pull_dispatches_the_expected_process()
  : void {
    $fakeRunner = new FakeProcessRunner([
      new ProcessResult(0),
    ]);

    $this->app->bind(RunsExternalProcesses::class, fn() => $fakeRunner);

    $exitCode = Artisan::call('git:pull');

    $this->assertSame(0, $exitCode);
    $this->assertSame([
      ['git', 'pull'],
    ], $fakeRunner->commands);
  }

  public function test_composer_install_dispatches_the_expected_process()
  : void {
    $fakeRunner = new FakeProcessRunner([
      new ProcessResult(0),
    ]);

    $this->app->bind(RunsExternalProcesses::class, fn() => $fakeRunner);

    $exitCode = Artisan::call('composer:install');

    $this->assertSame(0, $exitCode);
    $this->assertSame([
      ['composer', 'install'],
    ], $fakeRunner->commands);
  }
}
