<?php
include get_theme_file_path() . '/vendor/autoload.php';

function readSchema($schema)
{
  $json_content = file_get_contents(get_theme_file_path() . '/' . $schema);
  return json_decode($json_content, true);
}

function flatJsonschema($json)
{
  $defs = $json['definitions'];
  $data = [];
  function recursive($obj, $parentName, &$defs, &$data)
  {
    foreach ($obj['properties'] as $name => $property) {
      $newName = join('_', array_filter([$parentName, $name]));
      if ($property['type'] === 'object') {
        recursive($property, $name, $defs, $data);
      } else {
        if ($property['$ref']) {
          $def = str_replace('#/definitions/', '', $property['$ref']);
          if ($defs[$def]['type'] === 'object') {
            $final = ['properties' => []];
            $property['$ref'] = false;
            $final['properties'][$newName] = array_merge([], $property, $defs[$def]);
            recursive($final, $newName, $defs, $data);
          } else {
            $data[$newName] = $property;
          }
        } else {
          $data[$newName] = $property;
        }
      }
    }
  }
  recursive($json, null, $defs, $data);
  return $data;
}

function retrieveData($json, $post, $post_id)
{
  $flatKeys = flatJsonschema($json);
  $data = [];

  foreach ($flatKeys as $name => $field) {
    $id = 0;
    if (isset($post_id)) {
      $id = $post_id;
    } else {
      $id = $post->ID;
    }
    $data[$name] = get_post_meta($id, $name, true);
  }
  return $data;
}

function saveData($json, $post_id)
{
  $flatKeys = flatJsonschema($json);
  $data = [];

  foreach ($flatKeys as $name => $field) {
    if (is_array($_POST[$name])) {
      $_POST[$name] = implode('; ', $_POST[$name]);
    }
    $value = $_POST[$name];
    if ($field['type'] === 'file') {
      if (
        !file_exists($_FILES[$name]['tmp_name']) ||
        !is_uploaded_file($_FILES[$name]['tmp_name'])
      ) {
        continue;
      }
      $lastFile = get_post_meta($post_id, $name . '_path', true);
      if (file_exists($lastFile)) {
        unlink($lastFile);
      }
      $ext = end(explode('.', $_FILES[$name]['name']));
      $file = wp_upload_bits(
        $name . '-' . $post_id . '.' . $ext,
        null,
        file_get_contents($_FILES[$name]['tmp_name'])
      );
      if ($file['error']) {
        error_log($file['error']);
        return $file;
      }
      $value = str_replace(wp_upload_dir()['basedir'], "", $file['file']);
    }
    update_post_meta($post_id, $name, $value);
  }
  return $data;
}

function schema_meta_box_render($post, $nonce, $pugFile, $schema)
{
  wp_nonce_field($nonce, 'meta_box_nonce');

  $json_data = readSchema($schema);
  $data = retrieveData($json_data, $post, null);

  $pug = new Pug();
  $pug->displayFile(get_theme_file_path() . '/template/pug/' . $pugFile, [
    'site' => ['data' => ['schema' => $json_data, 'data' => $data]]
  ]);
}

function schema_save_meta($post_id, $nonce, $schema)
{
  if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], $nonce))
    return;

  if (!current_user_can('edit_post', $post_id))
    return;

  $json_data = readSchema($schema);
  $res = saveData($json_data, $post_id);
  if ($res['error']) {
    $error = new WP_Error(400, $res['error']);
  }
}

function error_log_array($array)
{
  foreach ($array as $key => $value) {
    error_log($key . ': ' . $value);
  }
}
