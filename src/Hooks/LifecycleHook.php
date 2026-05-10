<?php

declare(strict_types=1);

namespace PhpN8n\Client\Hooks;

enum LifecycleHook: string
{
    case BeforeWebhookRequest = 'webhook.before_request';
    case AfterWebhookResponse = 'webhook.after_response';
    case ExecutionReferenceCreated = 'execution.reference_created';

    case BeforeExecutionRefresh = 'execution.before_refresh';
    case AfterExecutionRefresh = 'execution.after_refresh';

    case BeforePollingStarts = 'execution.before_polling_starts';
    case PollingAttempt = 'execution.polling_attempt';
    case ExecutionStatusChanged = 'execution.status_changed';
    case ExecutionSucceeded = 'execution.succeeded';
    case ExecutionFailed = 'execution.failed';
    case ExecutionTimedOut = 'execution.timed_out';

    case ExceptionThrown = 'exception.thrown';
}
