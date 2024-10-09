<?php

/**
 * PBES2params
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
 * PBES2params
 *
 * from https://tools.ietf.org/html/rfc2898#appendix-A.3
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class PBES2params
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['keyDerivationFunc' => AlgorithmIdentifier::MAP, 'encryptionScheme' => AlgorithmIdentifier::MAP]];
}
