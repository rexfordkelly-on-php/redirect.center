<?php

$redirect_domain = "redirect.center";

$r = dns_get_record($_SERVER['HTTP_HOST'],DNS_A + DNS_CNAME);

if ($r[0]['type'] == "A") {

	# Verifica se existe a entrada redirect.center.HTTP_HOST
	$record = "redirect.".$_SERVER['HTTP_HOST'];
	$rr = dns_get_record($record,DNS_CNAME);

	redirect($rr[0]['type'],$record,$rr[0]['target']);

}

elseif ($r[0]['type'] == "CNAME") {

	redirect($r[0]['type'],$_SERVER['HTTP_HOST'],$r[0]['target']);

}

function redirect ($type,$record,$target) {

	global $redirect_domain;

	if ($type == "CNAME") {

        $code = 301;

        $target = str_replace(".".$redirect_domain,"",$target);

        # Verifica redirecionamento por URI
        if (strstr($target,".opts-uri")) {
            $target = str_replace(".opts-uri","",$target);
            $target .= $_SERVER['REQUEST_URI'];
        }

        # Muda codigo de redirect
        if (strstr($target,".opts-statuscode-")) {
            $code = strstr($target,".opts-statuscode-");
            $code = str_replace(".opts-statuscode-","",$code);
            $target = str_replace(".opts-statuscode-".$code,"",$target);
            $code = filter_var($code, FILTER_SANITIZE_NUMBER_INT);
        }

        Header('location: http://' . $target , true, $code);

	}

	else {

		// ERRO INDICANDO QUE DEVERIA SER DO TIPO CNAME
        print "<html><head><title>error</title></head><body><pre>\n";
        print "I can't resolve record: ".$record.".\n\n";
        print "Add in your dns server this entry:\n";
        print $redirect_domain.".".$_SERVER['HTTP_HOST']." CNAME your_redirect.".$redirect_domain.".\n\n";
        print "If it is already done, may you need wait to try again.\n\n";
        print "<a href='http://".$redirect_domain."'>".$redirect_center."</a>";
        print "</pre></body></html>";	
   	}

}
?>
