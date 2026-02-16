<?php
/**
 * Audio play posts functionality.
 *
 * @package Rock_Star
 */

/**
 * Prepend audio player to post content.
 *
 * @param string $content Post content.
 * @return string
 */
function rock_stars_prepend_post_audio_player( $content ) {
	if ( is_singular( 'post' ) && in_the_loop() && is_main_query() ) {

		$audio_url = carbon_get_post_meta( get_the_ID(), 'post_audio_file' );
		if ( $audio_url ) {

			$post_id      = get_the_ID();
			$uid          = 'rock-stars-' . $post_id; // VIP: Consistent prefix.
			$btn_id       = $uid . '-btn';
			$wrap_id      = $uid . '-wrap';
			$icon_id      = $uid . '-icon';
			$wave_id      = $uid . '-wave';
			$audio_dom_id = $uid . '-audio';

			ob_start();
			?>
			<div class="rs-listen-toggle-container" style="margin-top:50px;max-width:100%;width:100%;">
				<button
					id="<?php echo esc_attr( $btn_id ); ?>"
					aria-expanded="false"
					data-target="#<?php echo esc_attr( $wrap_id ); ?>"
					class="inline-flex items-center justify-center py-2 px-4 mr-4 mb-2 rounded-md bg-primary bg-opacity-10 text-body-color hover:bg-opacity-100 hover:text-white cursor-pointer border border-transparent transition"
					style="text-decoration:none;"
					type="button"
				>
					<span id="<?php echo esc_attr( $icon_id ); ?>" class="inline-block w-5 h-5 mr-2" aria-hidden="true">
						<!-- Play Icon -->
						<svg viewBox="0 0 24 24" fill="#2E3038" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
							<path d="M8 5v14l11-7z"/>
						</svg>
					</span>
					<span>Listen to the article</span>
				</button>

				<div
					id="<?php echo esc_attr( $wrap_id ); ?>"
					class="rs-post-audio-player-container"
				>
					<!-- Waveform -->
					<div id="<?php echo esc_attr( $wave_id ); ?>" class="rs-audio-wave"></div>

					<!-- Audio element controlled by Plyr; WaveSurfer reads from it -->
					<audio
						id="<?php echo esc_attr( $audio_dom_id ); ?>"
						class="js-player"
						controls
						crossorigin
						style="width:100%;height:60px;margin-top:8px;"
					>
						<source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mpeg" />
					</audio>
				</div>
			</div>
			<?php
			$player_html = ob_get_clean();
			$content    .= $player_html;

			// Hook inline logic once in footer.
			static $rock_stars_audio_inline_done = false;
			if ( ! $rock_stars_audio_inline_done ) {
				$rock_stars_audio_inline_done = true;
				add_action( 'wp_footer', 'rock_stars_audio_inline_assets', 99 );
			}
		}
	}
	return $content;
}
add_filter( 'the_content', 'rock_stars_prepend_post_audio_player' );

/**
 * Inline JS/CSS (one-time in the footer).
 */
function rock_stars_audio_inline_assets() {
	?>
	<script>
	document.addEventListener('DOMContentLoaded', () => {
		const playIconSVG = `
			<svg viewBox="0 0 24 24" fill="#2E3038" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
				<path d="M8 5v14l11-7z"/>
			</svg>
		`;
		const closeIconSVG = `
			<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
				<path d="M18 6L6 18" stroke="#2E3038" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M6 6L18 18" stroke="#2E3038" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		`;

		// For each player on the page.
		document.querySelectorAll('.rs-post-audio-player-container').forEach((wrap) => {
			const waveEl  = wrap.querySelector('.rs-audio-wave');
			const audioEl = wrap.querySelector('audio.js-player');
			// Match logic for various buttons.
			const btn     = wrap.parentElement.querySelector('button[id*="-btn"]');
			const icon    = wrap.parentElement.querySelector('span[id*="-icon"]');

			if ( ! btn || ! icon || ! window.Plyr || ! window.WaveSurfer ) {
				return;
			}

			// Plyr: Hide progress (managed via WaveSurfer).
			const plyrInstance = new Plyr(audioEl, {
				controls: ['play', 'current-time', 'mute', 'volume'],
			});

			// WaveSurfer: Load from existing <audio> element.
			const wavesurfer = WaveSurfer.create({
				container: waveEl,
				waveColor: '#2E3038',
				progressColor: '#4A6CF7',
				barWidth: 2,
				barGap: 2,
				barRadius: 1,
				height: 80,
				responsive: true,
				interact: true,
				dragToSeek: true,
				backend: 'MediaElement',
				media: audioEl,
			});

			// Seek handling.
			wavesurfer.on('seek', (progress) => {
				if (audioEl.duration) {
					audioEl.currentTime = progress * audioEl.duration;
				}
			});

			// Toggle expand/collapse.
			btn.addEventListener('click', () => {
				const expanded = btn.getAttribute('aria-expanded') === 'true';

				if (expanded) {
					plyrInstance.pause();
					wrap.classList.remove('is-open');
					btn.setAttribute('aria-expanded', 'false');
					icon.innerHTML = playIconSVG;
					btn.style.border = '';
				} else {
					wrap.classList.add('is-open');
					btn.setAttribute('aria-expanded', 'true');
					icon.innerHTML = closeIconSVG;
					btn.style.border = 'none';
				}
			});
		});
	});
	</script>

	<style>
	.rs-post-audio-player-container .plyr__progress,
	.rs-post-audio-player-container .plyr__progress__container {
		display:none !important;
	}

	.rs-post-audio-player-container {
		max-height:0;
		opacity:0;
		overflow:hidden;
		transition:opacity .4s ease,max-height .5s ease;
		border:1px solid #2E3038;
		border-radius:6px;
		--plyr-color-main:#4A6CF7;
		--plyr-track-background:#222;
		--plyr-progress-background:#4A6CF7;
		--plyr-control-hover-background:rgba(74,108,247,.15);
		color:#fff;
		margin-top:10px;
		padding:8px 12px 16px;
	}
	.rs-post-audio-player-container.is-open {
		max-height:300px;
		opacity:1;
	}

	.rs-post-audio-player-container .rs-audio-wave {
		width:100%;
		height:80px;
	}

	.rs-post-audio-player-container .plyr {
		width:100% !important;
		height:60px !important;
	}
	.rs-post-audio-player-container .plyr__time {
		color:#fff !important;
	}
	.plyr--audio .plyr__controls {
		background: rgba(0, 0, 0, 0) !important;
	}
	</style>
	<?php
}