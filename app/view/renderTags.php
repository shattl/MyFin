<?php foreach ($tag_list as $tag) : ?>
    <a href="?by_tag=<?php echo $tag['id']?>"><?php echo $tag['name']?></a>
<?php endforeach;