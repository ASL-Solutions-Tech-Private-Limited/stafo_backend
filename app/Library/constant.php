<?php

namespace App\Library;

class Constant
{
    const CHANNEL_ID           =  "ASDFGH12345";
    const BANK_CHANNEL_ID     =  "ASDFGH12345";
    const USER_ID_PREFIX       =  'ASLWLT';
    const APPNAME              =  "ASLWLT";
    const PENNY_DROP_CHARGE    =  3;
    const DMT_CHARGE          =  1;
    const AADHAR_PAY_CHARGE    =  1;
    const MIN_BAL_AMOUNT       =  0;

    /**
     * Application Settings
     */

    const LOGO_PATH       =  'public/admin/assets/img/logo.png';
    const EMAIL_COLOR     =  "#ffa73b";
    const SUPPORT_NO      =  "1800-212-8155";
    const ADDRESS         =  "Ceej - 1234 Main Street - Anywhere, MA - 56789";
    const SMS_SERVICE    =  "enable";  // use `enable` to enable and `disable` to disable sms service
    const CALLBACK_URL    =  "https://aslwallets.co.in/app/admin/CallBackController/index";
    const ONBOARD_CHARGE  =  0;

    const CASHFREE_API_URL    =  "https://payout-api.cashfree.com";

    /****************************************************  API Credentials  *********************************************************** */



    const PARTNERID        =  "PS001790";
    const JWTKEY           =  "UFMwMDE3OTA2MDYyMDliOTBkOGUxYTMzNTBhZjFhMGJjMGYwMThlMA==";
    const AUTHORISEDKEY    =  "MWJlZTkzOGJlNzk5YWFhZDZjN2M5ZWIzNTU2NGFmMjM=";
    const AESKEY           =  "cdce9d219562c15a";
    const AESIV            =  "9252c7f7bcbacb39";

    /******************cashfree api credentials**********************/
    const CASHFREE_USERNAME          =  "CF161850CG4TUCNP10CMT71UDED0";
    const CASHFREE_PASSWORD            =  "cf82affeb46cbc7cab3c4ad68f3c263d838f6fac";
    /***************************************************************/



    /**************************************************** ICICI PAYOUT API CREDENTIALS ********************************************** */


    const DEBIT_ACC     =  "";
    const AGGRID        =  "";
    const CORPID         =  "";
    const USERID        =  "";
    const URN           =  "";
    const AGGRNAME      =  "";
    const ALIASID       =  "";
    const CERT_PATH     =  "";
    const CERT_KEY      =  "";
    const API_KEY       =  "";

    const ENCPIN       =  "a36d519075e4c25d3005b48f4efea336";



    /**
     * Payworld Credentials
     */
    const MERCHANTID        =  "610930252";
    const MERCHANTKEY       =  "NZmMb7WQXjwWlPglAnLNk8XrdDh8VZH5CaHTnhzUP9k=";
    const HEADERSECRETKEY   =  "1Vx1IGJMp/8Y7oMQtJcr0gj3gMsIEUy0SyDMkousZ0c=";
    const CONTENTSECRETKEY  =  "qYGQoo6xA1CQ/Ex4trhQlWpS5ERu9Dkt1aKqSrOZK0Q=";




    /**
     * EZYTM API CRED
     */
    const APIUSERID       =  "4455";
    const APIPASSWORD     =  "9876543210Aa@";


    /**
     * Open Money Payout
     */
    const OPEN_MONEY_TOKEN = "7617fa10-8c43-11ed-a0b3-9bfa23042afe:b1cf73a7374d079e4f8407750504eb7a3aa6ec4d";
    const OPEN_MONEY_DEBIT_AC = "000405658800";
    //$config['open_money_debit_ac'] = "083505002661";

    /**
     * Open Money PG
     */
    const OPEN_ACCESS_KEY = "7617fa10-8c43-11ed-a0b3-9bfa23042afe";
    const OPEN_ACCESS_SECRET = "b1cf73a7374d079e4f8407750504eb7a3aa6ec4d";
    const OPEN_PG_URL = "https://payments.open.money/layer";
    const OPEN_PG_ENV = "live";

    /**
     * ASL wallet credentials
     */
    const ASL_TOKEN       =  "087005a117bc09f5eb5b3abef04d365c";

    /** Cashfree */
    //$config["app_id"]          = "189252bb8bf1fc76bba3a1894a252981";
    const APP_ID         = "1618505017d418fbee5434d51b058161";
    const SECRET_KEY      = "9668f9a016303bb7aa30b0e9ec24e025098de07a";
    //$config["secret_key"]      = "d4a516f0e3c5cadcb5c18cf4d0ccadfe4519a5d";

    /** Mplan Key **/
    const MPLANAPIKEY  = "97ff5219b67f08428f5812d9483e1bb3";



    /**** RazorpayX Payout */
    const RZP_USERNAME         = "rzp_live_EgF7jW4K1KOB76";
    const RZP_PASSWORD         = "9UuchMHRv6KXiwVfyccIZBob";
    const RZP_ACCOUNT_NO       = "4564567349055059";
}