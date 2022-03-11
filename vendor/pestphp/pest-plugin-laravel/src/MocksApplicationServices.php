<?php

declare(strict_types=1);

namespace Pest\Laravel;

use Illuminate\Foundation\Testing\TestCase;

/**
 * Specify a list of events that should be fired for the given operation.
 *
 * These events will be mocked, so that handlers will not actually be executed.
 *
 * @param array|string $events
 *
 * @return TestCase
 *
 * @throws \Exception
 */
function expectsEvents($events)
{
    return test()->expectsEvents(...func_get_args());
}

/**
 * Specify a list of events that should not be fired for the given operation.
 *
 * These events will be mocked, so that handlers will not actually be executed.
 *
 * @param array|string $events
 *
 * @return TestCase
 */
function doesntExpectEvents($events)
{
    return test()->doesntExpectEvents(...func_get_args());
}

/**
 * Mock the event dispatcher so all events are silenced and collected.
 *
 * @return TestCase
 */
function withoutEvents()
{
    return test()->withoutEvents(...func_get_args());
}

/**
 * Filter the given events against the fired events.
 */
function getFiredEvents(array $events): array
{
    return test()->getFiredEvents(...func_get_args());
}

/**
 * Specify a list of jobs that should be dispatched for the given operation.
 *
 * These jobs will be mocked, so that handlers will not actually be executed.
 *
 * @param array|string $jobs
 *
 * @return TestCase
 */
function expectsJobs($jobs)
{
    return test()->expectsJobs(...func_get_args());
}

/**
 * Specify a list of jobs that should not be dispatched for the given operation.
 *
 * These jobs will be mocked, so that handlers will not actually be executed.
 *
 * @param array|string $jobs
 *
 * @return TestCase
 */
function doesntExpectJobs($jobs)
{
    return test()->doesntExpectJobs(...func_get_args());
}

/**
 * Mock the job dispatcher so all jobs are silenced and collected.
 *
 * @return TestCase
 */
function withoutJobs()
{
    return test()->withoutJobs(...func_get_args());
}

/**
 * Filter the given jobs against the dispatched jobs.
 */
function getDispatchedJobs(array $jobs): array
{
    return test()->getDispatchedJobs(...func_get_args());
}

/**
 * Filter the given classes against an array of dispatched classes.
 */
function getDispatched(array $classes, array $dispatched): array
{
    return test()->getDispatched(...func_get_args());
}

/**
 * Check if the given class exists in an array of dispatched classes.
 */
function wasDispatched(string $needle, array $haystack): bool
{
    return test()->wasDispatched(...func_get_args());
}

/**
 * Mock the notification dispatcher so all notifications are silenced.
 *
 * @return TestCase
 */
function withoutNotifications()
{
    return test()->withoutNotifications(...func_get_args());
}

/**
 * Specify a notification that is expected to be dispatched.
 *
 * @param mixed $notifiable
 *
 * @return TestCase
 */
function expectsNotification($notifiable, string $notification)
{
    return test()->expectsNotification(...func_get_args());
}
