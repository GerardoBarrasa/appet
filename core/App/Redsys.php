<?php

class Redsys
{
	public static function tpvPay($total_pedido,$id_pedido_tmp)
	{
		$idioma = Idiomas::getLangBySlug($_SESSION['lang']);
		$id_idioma = '0';
		if( $idioma->id == 1 )
			$id_idioma = '1';
		elseif( $idioma->id == 2 )
			$id_idioma = '2';

		require_once _PATH_.'core/Helpers/RedsysAPI/apiRedsys.php';

		$miObj = new \RedsysAPI;

		$Ds_Merchant_Amount = $total_pedido*100;
		$Ds_Merchant_Order = rand(100,999).'/'.$id_pedido_tmp;
		$Ds_Merchant_MerchantCode = tpv_merchant_code;
		$Ds_Merchant_Currency = '978';
		$Ds_Merchant_Transaction_Type = '0';
		$Ds_Merchant_Terminal = '001';
		$Ds_Merchant_MerchantURL = _DOMINIO_.'confirma-tpv.php';
		$Ds_Merchant_Url = tpv_url;
		$Ds_Merchant_UrlOK = Slugs::getCurrentSlugByModId('confirmacion-reserva');
		$Ds_Merchant_UrlKO = Slugs::getCurrentSlugByModId('error-pago');
		$signatureKey = tpv_clave;

		$miObj->setParameter("DS_MERCHANT_AMOUNT", (string)$Ds_Merchant_Amount);
		$miObj->setParameter("DS_MERCHANT_ORDER", $Ds_Merchant_Order);
		$miObj->setParameter("DS_MERCHANT_MERCHANTCODE", $Ds_Merchant_MerchantCode);
		$miObj->setParameter("DS_MERCHANT_CURRENCY", $Ds_Merchant_Currency);
		$miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $Ds_Merchant_Transaction_Type);
		$miObj->setParameter("DS_MERCHANT_TERMINAL", $Ds_Merchant_Terminal);
		$miObj->setParameter("DS_MERCHANT_MERCHANTURL", $Ds_Merchant_MerchantURL);
		$miObj->setParameter("DS_MERCHANT_URLOK", $Ds_Merchant_UrlOK);
		$miObj->setParameter("DS_MERCHANT_URLKO", $Ds_Merchant_UrlKO);
		$miObj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", $id_idioma);
		$miObj->setParameter("DS_MERCHANT_MERCHANTDATA", $id_pedido_tmp);

		$params = $miObj->createMerchantParameters();
		$signature = $miObj->createMerchantSignature($signatureKey);

		$html = 
		'
			<form style="display:none;" action="'.$Ds_Merchant_Url.'" name="tpv" id="tpv" method="POST">
				<input type="text" name="Ds_SignatureVersion" value="HMAC_SHA256_V1"/>
				<input type="text" name="Ds_MerchantParameters" value="'.$params.'"/>
				<input type="text" name="Ds_Signature" value="'.$signature.'"/>
			</form>
			    <script type="text/javascript">
					function addLoadEvent(func) 
					{
					  	var oldonload = window.onload;
					  	if (typeof window.onload != "function") 
					  	{
							window.onload = func;
					  	} 
					  	else 
					 	{
							window.onload = function() 
							{
						  		if (oldonload) 
						  		{
									oldonload();
						  		}
						  		func();
							}
					  	}
					}
					addLoadEvent(function() 
					{
					 	document.getElementById("tpv").submit();
					});
				</script>
		';
		            
		return $html;
	}
}
