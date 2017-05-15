<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
    //
    public function store(Request $request){
        $directPaymentRequest = new \PagSeguroDirectPaymentRequest();
        $directPaymentRequest -> setPaymentMode('DEFAULT'); // GATEWAY
        $directPaymentRequest -> setPaymentMethod($request->get('method'));

        $directPaymentRequest->setCurrency('BRL');

        $items = $request->get('items');
        foreach ($items as $key => $item){
            $directPaymentRequest->addItem("00$key", $item['name'], 1, $item['price']);
        }

        $directPaymentRequest->setSender(
            'João Comprador',
            'joao@sandbox.pagseguro.com.br',
            '11',
            '56273440',
            'CPF',
            '156.009.442-76'
        );

        $directPaymentRequest -> setSenderHash($request->get('hash'));

        $installments = new \PagSeguroDirectPaymentInstallment([
            'quantity' => 1,
            'value' => $request->get('total')
        ]);

        $sedexCode = \PagSeguroShippingType::getCodeByType('SEDEX');
        $directPaymentRequest->setShippingType($sedexCode);
        $directPaymentRequest->setShippingAddress(
            '01452002',
            'Av. Brig. Faria Lima',
            '1384',
            'apt. 114',
            'Jardim Paulistano',
            'São Paulo',
            'SP',
            'BRA'
        );

        /*** */
        $billingAddress = new \PagSeguroBilling(
            array(
                'postalCode' => '01452002',
                'street' => 'Av. Brig. Faria Lima',
                'number' => '1384',
                'complement' => 'apt. 114',
                'district' => 'Jardim Paulistano',
                'city' => 'São Paulo',
                'state' => 'SP',
                'country' => 'BRA'
            )
        );

        $creditCardData = new \PagSeguroCreditCardCheckout(
            array(
                'token' => $request->get('token'),
                'installment' => $installments,
                'billing' => $billingAddress,
                'holder' => new \PagSeguroCreditCardHolder(
                    array(
                        'name' => 'João Comprador',
                        'birthDate' => date('01/10/1979'),
                        'areaCode' => '11',
                        'number' => '56273440',
                        'documents' => array(
                            'type' => 'CPF',
                            'value' => '156.009.442-76'
                        )
                    )
                )
            )
        );

        $directPaymentRequest->setCreditCard($creditCardData);

        try{
            $credentials = \PagSeguroConfig::getAccountCredentials();
            $response = $directPaymentRequest->register($credentials);
            dd($response);
            /*return[
                'finalized' => true,
            ];*/
        }catch (\PagSeguroServiceException $e){
            return [
                'message' => $e->getMessage(),
                'success' => false
            ];
        }
    }
}
