<? foreach ($this->categories as $categorie) : ?>
	<div class="cat_block">
		<span><?= $categorie->name; ?></span>
		<? foreach ($categorie->forum->order_by('forum_order') as $forum) : ?>
			<div class="forum_block">
			<?= _a($forum->name, 'forum:forum:forum', array('id'=>$forum->id)); ?>
			</div>
		<? endforeach; ?>
	</div>
<? endforeach; ?>