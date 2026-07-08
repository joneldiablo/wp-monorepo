<?php
if (strpos($_SERVER["REQUEST_URI"], 'save-form')) {
  $response = ['success' => true];
  switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      $slug = sanitize_title(time());
      $post_id = wp_insert_post(
        array(
          'post_name'   => $slug,
          'post_title' => $slug,
          'post_status'   => 'private',
          'post_type'   => 'complaint'
        )
      );
      $json_data = readSchema('denuncia.json');
      $res = saveData($json_data, $post_id);
      if ($res['error']) {
        $response = $res;
      }
      break;
    default:
      $response = ['error' => true, 'description' => 'method not allowed'];
      break;
  }
  echo json_encode($response);
  exit();
} elseif (strpos($_SERVER["REQUEST_URI"], 'add-like-post') && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $response = ['success' => true];
  $likes_name = 'likes';
  $id = (int) end(explode('/', $_SERVER["REQUEST_URI"]));
  $post = get_post($id);
  $likes = (int) get_post_meta($id, $likes_name, true);
  update_post_meta($id, $likes_name, ++$likes);
  echo json_encode($response);
  exit();
}
