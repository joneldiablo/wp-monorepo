<?php

use Dompdf\Dompdf;

$baseUrl = dirname(__DIR__) . '/form-agent-register/';
include $baseUrl . 'vendor/autoload.php';

$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
$url = $protocol . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if (str_replace(get_option('siteurl'), '', $url) !== '/') {
  echo '404<br>' . $url;
  exit();
}

switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':
    $slug = sanitize_title($_POST['contact_email']);
    $response = ['success' => true];
    $args = array(
      'name'   => $slug,
      'post_type'   => 'register',
      'post_status'   => 'private',
      'numberposts' => 1
    );
    $exist = get_posts($args);

    if (!$exist) {
      $post_id = wp_insert_post(
        array(
          'post_name'   => $slug,
          'post_status'   => 'private',
          'post_type'   => 'register'
        )
      );
      $json_data = readSchema('register.json');
      $res = saveData($json_data, $post_id);
      if ($res['error']) {
        $response = $res;
      } else {
        //firstPage contrato
        $contratoPage1 = $baseUrl . 'files/html/index.html';
        $contratoPage15 = $baseUrl . 'files/html/last-page.html';
        $content = file_get_contents($contratoPage1);
        $content = str_replace('assets/', $baseUrl . 'files/html/assets/', $content);
        $content15 = file_get_contents($contratoPage15);
        $content15 = str_replace('assets/', $baseUrl . 'files/html/assets/', $content15);
        $emailMsg = '';
        $dataFile = $_POST;
        setlocale(LC_ALL, "es_MX");
        $dataFile['date'] = date('d') . '/' . date('M') . '/' . date('Y');
        foreach ($dataFile as $i => $field) {
          $content = str_replace('${' . $i . '}', $field, $content);
          $content15 = str_replace('${' . $i . '}', $field, $content15);
          $emailMsg .= '<p>' . $i . ': <b>' . $field . '</b></p>';
        }
        //page first
        $dompdf = new Dompdf();
        $dompdf->set_option('chroot', $baseUrl);
        $dompdf->loadHtml($content);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $pdfPage1 = $baseUrl . 'files/' . $slug . '-contrato-page-1.pdf';
        file_put_contents($pdfPage1, $output);
        //page last
        $dompdf = new Dompdf();
        $dompdf->set_option('chroot', $baseUrl);
        $dompdf->loadHtml($content15);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $pdfPage15 = $baseUrl . 'files/' . $slug . '-contrato-page-15.pdf';
        file_put_contents($pdfPage15, $output);

        // response only one file -- join html files & pdf files
        $pdf = new \Jurosh\PDFMerge\PDFMerger;
        $pdf->addPDF($pdfPage1, 'all', 'vertical');
        $files = glob($baseUrl . 'files/pdf/*.pdf');
        foreach ($files as $file) {
          $pdf->addPDF($file, 'all', 'vertical');
        }
        $pdf->addPDF($pdfPage15, 'all', 'vertical');

        // path to final file
        $contratos = wp_get_upload_dir()['basedir'] . '/contratos';
        if (!file_exists($contratos)) {
          mkdir($contratos, 0777, true);
        }
        $contratoFinal = $contratos . '/' . $slug . '-contrato.pdf';
        $pdf->merge('file', $contratoFinal);
        update_post_meta($post_id, 'contrato', $slug . '-contrato.pdf');

        unlink($pdfPage1);
        unlink($pdfPage15);

        // send email
        $attachments = array(
          $contratoFinal,
          get_post_meta($post_id, 'documents_agentId_path', true),
          get_post_meta($post_id, 'documents_agentIdBack_path', true),
          get_post_meta($post_id, 'documents_account_path', true)
        );
        $headers = array(
          'Content-Type: text/html; charset=UTF-8',
          'From: Registro de Agentes Táctika <contacto@tactika.mx>'
        );
        $send = wp_mail($GLOBALS['cgv']['email_registers'], 'Nuevo registro: ' . get_post_meta($post_id, 'contact_email', true), $emailMsg, $headers, $attachments);

        $response['data'] =  base64_encode(file_get_contents($contratoFinal));
      }
    } else {
      $response = ['error' => true, 'status' => 400];
    }
    //set_time_limit(10);
    echo json_encode($response);
    break;

  default:
    $json_content = file_get_contents($baseUrl . 'register.json');
    $json_data = json_decode($json_content, true);

    echo get_header();
    $pug = new Pug();
    $pug->displayFile($baseUrl . 'template/pug/form.pug', [
      'site' => [
        'data' => ['schema' => $json_data],
        'home' => get_home_url(),
        'assets' => get_stylesheet_directory_uri() . '/assets/'
      ]
    ]);
    echo get_footer();
    break;
}
exit();
