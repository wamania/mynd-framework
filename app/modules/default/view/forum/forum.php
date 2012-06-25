<div class="cat_block">
	<span><?= $this->forum->name; ?></span>
	<? foreach ($this->forum->sujet->order_by('updated_on') as $forum) : ?>
		<div class="forum_block">
		<?= _a($forum->name, 'forum:forum:forum', array('id'=>$forum->id)); ?>
		</div>
	<? endforeach; ?>
</div>