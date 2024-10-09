<?php

namespace Rank_Math_Instant_Indexing\GuzzleHttp;

use Rank_Math_Instant_Indexing\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
