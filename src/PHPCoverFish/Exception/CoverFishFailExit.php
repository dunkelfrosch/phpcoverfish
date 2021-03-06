<?php

namespace DF\PHPCoverFish\Exception;

/**
 * Class CoverFishFailExit
 *
 * @package    DF\PHPCoverFish
 * @author     Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright  2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license    http://www.opensource.org/licenses/MIT
 * @link       http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since      class available since Release 0.9.0
 * @version    0.9.6
 *
 * @codeCoverageIgnore
 */
class CoverFishFailExit extends \Exception
{
    // validator based scan fail detected
    const RETURN_CODE_SCAN_FAIL = 1;
    // exception based scan fail detected
    const RETURN_CODE_SCAN_ERROR = 2;
    // internal error detected
    const RETURN_CODE_INTERNAL_FAIL = 1;
}