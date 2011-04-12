<?php
class Kp_Plugins_Authorizenet_Native extends Kp_Plugins_Authorizenet_Native_Abstract
{
    public function buildGatewayString($transaction)
    {
        $gateway_string = "";

        $transaction_type = "";

        switch($transaction->getTransactionType())
        {
            case $transaction->TRANSACTION_TYPE_AUTH_ONLY:
                $transaction_type = "AUTH_ONLY";
                $trans_id       = '';
            break;

            case $transaction->TRANSACTION_TYPE_SALE:
                $transaction_type = "AUTH_CAPTURE";
                $trans_id       = '';
            break;

            case $transaction->TRANSACTION_TYPE_PRIOR_AUTH:
                $transaction_type = "PRIOR_AUTH_CAPTURE";
                $trans_id       = '';
            break;

            case $transaction->TRANSACTION_TYPE_CREDIT:
                $transaction_type = "CREDIT";
                $trans_id       = '&x_trans_id=' . $transaction_data->getTransactionID();
            break;
        }
        $test_request = "FALSE";
        if ($this)
        {
            $test_request = "TRUE";
        }
        $expiration_date = $transaction->getFundingSourtce()->getExpirationMonth() . $transaction->getFundingSourtce()->getExpirationMonthYear();

        $first_name = '';
        $last_name = '';
        $temp = explode(' ', $transaction->getFundingSourtce()->getName());
        if (is_array($temp) && count($temp) >= 2) {
            $first_name = $temp[0];
            $last_name  = $temp[1];
        }

        $gateway_string .= "x_relay_response=FALSE&x_Test_Request=$test_request&x_Delim_Char=|&x_Version=3.1&x_Method=CC&x_Delim_Data=True&x_Login=$this->gateway_user_id&x_Tran_Key=$this->gateway_user_key&";
        $gateway_string .= "x_Type=$transaction_type&x_Amount=$transaction_data->amount&x_Description=$transaction_data->order_description&x_Card_Num=$credit_card->card_number&x_Exp_Date=$expiration_date&x_Card_Code=$credit_card->card_verification_number&";
        $gateway_string .= "x_Company=$credit_card->company&x_First_Name=$first_name&x_Last_Name=$last_name&x_Address=$credit_card->address_1&x_City=$credit_card->city&";
        $gateway_string .= "x_State=$credit_card->state&x_Zip=$credit_card->postal_code&x_Country=$credit_card->country&x_Phone=$credit_card->day_phone&x_Cust_ID=$transaction_data->customer_number&x_Email=$credit_card->email_address&";
        $gateway_string .= "x_tax_exempt=Y&";

        return $gateway_string;
    }
}