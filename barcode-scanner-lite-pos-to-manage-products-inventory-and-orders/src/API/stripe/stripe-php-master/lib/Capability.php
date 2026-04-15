<?php


namespace Stripe;

class Capability extends ApiResource
{
    const OBJECT_NAME = 'capability';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';
    const STATUS_UNREQUESTED = 'unrequested';

    public function instanceUrl()
    {
        $id = $this['id'];
        $account = $this['account'];
        if (!$id) {
            throw new Exception\UnexpectedValueException(
                'Could not determine which URL to request: '
                . "class instance has invalid ID: {$id}",
                null
            );
        }
        $id = Util\Util::utf8($id);
        $account = Util\Util::utf8($account);

        $base = Account::classUrl();
        $accountExtn = \urlencode($account);
        $extn = \urlencode($id);

        return "{$base}/{$accountExtn}/capabilities/{$extn}";
    }

    public static function retrieve($_id, $_opts = null)
    {
        $msg = 'Capabilities cannot be retrieved without an account ID. '
               . 'Retrieve a capability using `Account::retrieveCapability('
               . "'account_id', 'capability_id')`.";

        throw new Exception\BadMethodCallException($msg);
    }

    public static function update($_id, $_params = null, $_options = null)
    {
        $msg = 'Capabilities cannot be updated without an account ID. '
               . 'Update a capability using `Account::updateCapability('
               . "'account_id', 'capability_id', \$updateParams)`.";

        throw new Exception\BadMethodCallException($msg);
    }

    public function save($opts = null)
    {
        $params = $this->serializeParameters();
        if (\count($params) > 0) {
            $url = $this->instanceUrl();
            list($response, $opts) = $this->_request('post', $url, $params, $opts, ['save']);
            $this->refreshFrom($response, $opts);
        }

        return $this;
    }
}
