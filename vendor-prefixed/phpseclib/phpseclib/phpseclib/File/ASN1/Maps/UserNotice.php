<?php

/**
 * UserNotice
 *
 * PHP version 5
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace Rank_Math_Instant_Indexing\phpseclib3\File\ASN1\Maps;

use Rank_Math_Instant_Indexing\phpseclib3\File\ASN1;
/**
 * UserNotice
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class UserNotice
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['noticeRef' => ['optional' => \true, 'implicit' => \true] + NoticeReference::MAP, 'explicitText' => ['optional' => \true, 'implicit' => \true] + DisplayText::MAP]];
}
