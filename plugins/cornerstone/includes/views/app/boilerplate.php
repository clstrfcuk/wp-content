<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<title><?php $this->title(); ?></title>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php $this->head(); ?>
</head>
<body<?php $this->body_classes(); ?>>
<?php $this->footer(); ?>
</body>
</html>
