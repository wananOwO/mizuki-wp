<?php
/**
 * Template Name: 追番
 *
 * 追番页面模板 - 以网格卡片形式展示所有追番条目。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="anime-page onload-animation">
	<div class="card-base px-6 md:px-9 py-6">
		<h1 class="transition w-full block font-bold mb-6 text-3xl md:text-4xl text-90">
			<?php esc_html_e( '追番', 'mizuki' ); ?>
		</h1>
		<div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-6"></div>

		<?php
		// 获取追番列表数据（支持本地/Bangumi/Bilibili）
		$anime_list = mizuki_get_anime_list();

		$status_labels = array(
			'watching'  => __( '在看', 'mizuki' ),
			'completed' => __( '看完', 'mizuki' ),
			'planned'   => __( '想看', 'mizuki' ),
			'onhold'    => __( '暂停', 'mizuki' ),
			'dropped'   => __( '放弃', 'mizuki' ),
		);

		$status_colors = array(
			'watching'  => 'bg-green-500/80 text-white',
			'completed' => 'bg-[var(--primary)]/80 text-white',
			'planned'   => 'bg-gray-500/80 text-white',
			'onhold'    => 'bg-yellow-500/80 text-white',
			'dropped'   => 'bg-red-500/80 text-white',
		);

		if ( ! empty( $anime_list ) ) :
		?>
		<!-- 状态筛选 -->
		<div class="anime-filter-bar flex flex-wrap gap-2 mb-6">
			<button type="button" class="anime-filter-tag btn-regular active px-4 py-1.5 rounded-lg text-sm font-medium bg-[var(--primary)] text-white" data-status="all"><?php esc_html_e( '全部', 'mizuki' ); ?></button>
			<button type="button" class="anime-filter-tag btn-regular px-4 py-1.5 rounded-lg text-sm font-medium" data-status="watching"><?php echo esc_html( $status_labels['watching'] ); ?></button>
			<button type="button" class="anime-filter-tag btn-regular px-4 py-1.5 rounded-lg text-sm font-medium" data-status="planned"><?php echo esc_html( $status_labels['planned'] ); ?></button>
			<button type="button" class="anime-filter-tag btn-regular px-4 py-1.5 rounded-lg text-sm font-medium" data-status="completed"><?php echo esc_html( $status_labels['completed'] ); ?></button>
		</div>

		<div class="anime-grid grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
			<?php foreach ( $anime_list as $anime ) :
				$status           = isset( $anime['status'] ) ? $anime['status'] : 'planned';
				$title            = isset( $anime['title'] ) ? $anime['title'] : '';
				$cover            = isset( $anime['cover'] ) ? $anime['cover'] : '';
				$rating           = isset( $anime['rating'] ) ? $anime['rating'] : 0;
				$link             = isset( $anime['link'] ) ? $anime['link'] : '#';
				$description      = isset( $anime['description'] ) ? $anime['description'] : '';
				$progress         = isset( $anime['progress'] ) ? intval( $anime['progress'] ) : 0;
				$total_episodes   = isset( $anime['totalEpisodes'] ) ? intval( $anime['totalEpisodes'] ) : 0;
				$progress_percent = isset( $anime['progressPercent'] ) ? floatval( $anime['progressPercent'] ) : 0;

				// 格式化进度显示
				$progress_text = '';
				if ( $total_episodes > 0 ) {
					$progress_text = sprintf( '%d/%d', $progress, $total_episodes );
				} elseif ( $progress > 0 ) {
					$progress_text = sprintf( '%d', $progress );
				}
			?>
			<div class="anime-card group relative bg-[var(--card-bg)] border border-[var(--line-divider)] rounded-[var(--radius-large)] overflow-hidden hover:shadow-lg transition" data-status="<?php echo esc_attr( $status ); ?>">
				<div class="relative aspect-[2/3] overflow-hidden">
					<?php if ( $cover ) : ?>
						<a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
							<img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-110" loading="lazy">
							<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
								<div class="absolute inset-0 flex items-center justify-center">
									<div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center">
										<svg class="w-6 h-6 text-gray-800 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
									</div>
								</div>
							</div>
						</a>
					<?php else : ?>
						<div class="w-full h-full flex items-center justify-center bg-[var(--primary)]/10">
							<span class="text-3xl font-bold text-[var(--primary)]/40 select-none px-2 text-center"><?php echo esc_html( mb_substr( $title, 0, 1 ) ); ?></span>
						</div>
					<?php endif; ?>

					<?php if ( $status && isset( $status_labels[ $status ] ) ) : ?>
					<span class="absolute top-2 left-2 px-2 py-1 rounded-md text-xs font-medium <?php echo esc_attr( $status_colors[ $status ] ); ?>">
						<?php echo esc_html( $status_labels[ $status ] ); ?>
					</span>
					<?php endif; ?>

					<?php if ( $rating > 0 ) : ?>
					<span class="absolute top-2 right-2 bg-black/70 text-white px-2 py-1 rounded-md text-xs font-medium flex items-center gap-1">
						<svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
						<span><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
					</span>
					<?php endif; ?>

					<?php if ( 'watching' === $status && $progress_percent > 0 ) : ?>
					<div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-2">
						<div class="w-full bg-white/20 rounded-full h-1.5 mb-1">
							<div class="bg-gradient-to-r from-emerald-400 to-teal-400 h-1.5 rounded-full transition-all duration-300" style="width: <?php echo esc_attr( $progress_percent ); ?>%"></div>
						</div>
						<div class="text-white text-xs font-medium"><?php echo esc_html( $progress_text ); ?></div>
					</div>
					<?php endif; ?>
				</div>

				<div class="p-3">
					<h3 class="text-sm font-bold text-black/90 dark:text-white/90 mb-1 leading-tight">
						<a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-[var(--primary)] transition-colors"><?php echo esc_html( $title ); ?></a>
					</h3>
					<?php if ( $description ) : ?>
					<p class="text-black/60 dark:text-white/60 text-xs mb-2 line-clamp-2">
						<?php echo esc_html( wp_strip_all_tags( $description ) ); ?>
					</p>
					<?php endif; ?>
					<?php if ( $progress_text && 'watching' !== $status ) : ?>
					<div class="text-xs text-black/50 dark:text-white/50"><?php echo esc_html( $progress_text ); ?></div>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<p class="anime-empty-filter text-50 text-center py-12 hidden"><?php esc_html_e( '没有符合条件的条目。', 'mizuki' ); ?></p>
		<?php else : ?>
		<p class="text-50 text-center py-12"><?php esc_html_e( '暂无追番内容。请在外观 → 自定义 → 追番API 中配置数据源。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>

	<script>
	( function () {
		var page = document.querySelector( '.anime-page' );
		if ( ! page ) { return; }
		var tags  = page.querySelectorAll( '.anime-filter-tag' );
		var cards = page.querySelectorAll( '.anime-card' );
		var empty = page.querySelector( '.anime-empty-filter' );
		if ( ! tags.length || ! cards.length ) { return; }

		function applyFilter( status ) {
			var visible = 0;
			cards.forEach( function ( card ) {
				var match = ( status === 'all' ) || ( card.getAttribute( 'data-status' ) === status );
				card.style.display = match ? '' : 'none';
				if ( match ) { visible++; }
			} );
			if ( empty ) { empty.classList.toggle( 'hidden', visible !== 0 ); }
		}

		tags.forEach( function ( tag ) {
			tag.addEventListener( 'click', function () {
				tags.forEach( function ( t ) {
					t.classList.remove( 'active', 'bg-[var(--primary)]', 'text-white' );
				} );
				tag.classList.add( 'active', 'bg-[var(--primary)]', 'text-white' );
				applyFilter( tag.getAttribute( 'data-status' ) || 'all' );
			} );
		} );
	} )();
	</script>
</main>
<?php
get_footer();
