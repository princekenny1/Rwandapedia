<?php
/**
 * Template for displaying single course
 *
 * @package         Edumall/TutorLMS/Templates
 * @theme-since     1.0.0
 * @theme-version   2.9.3
 */

defined( 'ABSPATH' ) || exit;

global $edumall_course;

$title_bar_type   = Edumall_Global::instance()->get_title_bar_type();
$top_info_classes = 'tutor-full-width-course-top tutor-course-top-info';

if ( '04' === $title_bar_type ) {
	$top_info_classes .= ' course-top-info-light';
}
?>

<div class="page-content tm-sticky-parent">

	<?php do_action( 'tutor_course/single/before/wrap' ); ?>

	<div <?php tutor_post_class(); ?>>
		<div class="<?php echo esc_attr( $top_info_classes ); ?>">
			<div class="container">
				<div class="row">
					<div class="col-lg-8">
						<?php tutor_course_lead_info(); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="tutor-full-width-course-body">
			<div class="container">
				<div class="row">
					<div class="col-lg-6">
						<div class="tutor-single-course-main-content tm-sticky-column">

							<?php do_action( 'tutor_course/single/before/inner-wrap' ); ?>

							<?php if ( $edumall_course->is_enrolled() ) : ?>
								<?php tutor_course_completing_progress_bar(); ?>
							<?php endif; ?>

							<?php Edumall_Tutor::instance()->course_prerequisites(); ?>
							<?php tutor_course_content(); ?>
							<?php tutor_course_benefits_html(); ?>
							<?php tutor_course_requirements_html(); ?>
							<?php tutor_course_target_audience_html(); ?>
							<?php tutor_course_topics(); ?>

							<?php if ( $edumall_course->is_viewable() ) : ?>
								<?php Edumall_Tutor::instance()->single_course_attachment_html(); ?>
								<?php tutor_course_question_and_answer(); ?>
							<?php endif; ?>

							<?php tutor_course_instructors_html(); ?>

							<?php if ( $edumall_course->is_viewable() ) : ?>
								<?php tutor_course_announcements(); ?>
							<?php endif; ?>

							<?php if ( $edumall_course->is_viewable() && $edumall_course->has_classroom_stream() ) : ?>
								<?php do_action( 'tutor_course/single/enrolled/google-classroom-stream', get_the_ID() ); ?>
							<?php endif; ?>

							<?php if ( $edumall_course->is_enrolled() ): ?>
								<?php do_action( 'tutor_course/single/enrolled/gradebook', get_the_ID() ); ?>
							<?php endif; ?>

							<?php tutor_course_target_reviews_html(); ?>

							<?php do_action( 'tutor_course/single/after/inner-wrap' ); ?>

						</div>
					</div>
					<div class="col-lg-6">
						<div class="tutor-single-course-sidebar tm-sticky-column">

							<?php do_action( 'tutor_course/single/before/sidebar' ); ?>

							<?php tutor_load_template( 'single.course.course-entry-box' ); ?>

							<?php do_action( 'tutor_course/single/after/sidebar' ); ?>

						<?php
/**
 * Template for displaying single course
 *
 * @author        Themeum
 * @url https://themeum.com
 *
 * @package       TutorLMS/Templates
 * @since         1.0.0
 * @version       1.4.3
 *
 * @theme-since   1.0.0
 * @theme-version 2.7.7
 */

defined( 'ABSPATH' ) || exit;

global $edumall_course;
$topics    = $edumall_course->get_topics();
$course_id = get_the_ID();
?>

<?php do_action( 'tutor_course/single/before/topics' ); ?>

<?php if ( $topics->have_posts() ) { ?>
	<div class="tutor-single-course-segment tutor-course-topics-wrap" style="background-color:#f8f1cf;width: 504px;margin-top:25px;">
		<div class="tutor-course-topics-header" style="border-bottom:1px solid #005658">
			<div class="tutor-course-topics-header-left">
				<h4 class="tutor-segment-title" style="margin:12px;"><?php esc_html_e( 'Curriculum', 'edumall' ); ?></h4>
			</div>
			<div class="tutor-course-topics-header-right">
				<?php
				$tutor_lesson_count = $edumall_course->get_lesson_count();

				if ( $tutor_lesson_count ) {
					echo '<span class="topics-total-lessons">' . sprintf( _n( '%s Lesson', '%s Lessons', $tutor_lesson_count, 'edumall' ), $tutor_lesson_count ) . '</span>';
				}

				$tutor_course_duration = Edumall_Tutor::instance()->get_course_duration_short_text( $course_id );
				if ( $tutor_course_duration ) {
					echo '<span class="topics-total-duration">' . $tutor_course_duration . '</span>';
				}
				?>
			</div>
		</div>
		<div class="tutor-course-topics-contents">
			<?php

			$index = 0;

			if ( $topics->have_posts() ) :
				while ( $topics->have_posts() ) :
					$topics->the_post();
					$topic_summery = get_the_content();
					$index++;

					$topic_wrap_class = 'tutor-course-topic tutor-topics-in-single-lesson';
					$title_wrap_class = 'tutor-course-title';

					if ( $index == 1 ) {
						$topic_wrap_class .= ' tutor-active';
					}

					if ( ! empty( $topic_summery ) ) {
						$title_wrap_class .= ' has-summery';
					}
					?>
					<div class="<?php echo esc_attr( $topic_wrap_class ); ?>">
						<div class="<?php echo esc_attr( $title_wrap_class ); ?>">
							<h4>
								<i class="tutor-icon-plus"></i>
								<?php the_title(); ?>
								<?php if ( $topic_summery ) : ?>
									<?php echo "<span class='topic-toggle-description toggle-information-icon'><i class='fas fa-info'></i></span>"; ?>
								<?php endif; ?>
							</h4>
						</div>
						<?php if ( ! empty( $topic_summery ) ) : ?>
							<div class="tutor-topics-summery" style="border-bottom:1px solid #005658;">
								<?php echo $topic_summery; ?>
							</div>
						<?php endif; ?>

						<div class="tutor-course-lessons"
							<?php if ( $index > 1 ): ?>
								style="display: none;"
							<?php endif; ?>
						>
							<?php
							$lessons = tutor_utils()->get_course_contents_by_topic( get_the_ID(), -1 );

							if ( $lessons->have_posts() ) :
								while ( $lessons->have_posts() ) :
									$lessons->the_post();
									global $post;

									$video = tutor_utils()->get_video_info();

									$play_time = false;
									if ( $video ) {
										$play_time = $video->playtime;
									}

									$lesson_icon = $play_time ? 'far fa-play-circle' : 'far fa-file-alt';

									if ( $post->post_type === 'tutor_quiz' ) {
										$lesson_icon = 'far fa-question-circle';
									}
									if ( $post->post_type === 'tutor_assignments' ) {
										$lesson_icon = 'far fa-file-edit';
									}
									?>

									<div class="tutor-course-lesson">
										<h5>
											<?php
											$lesson_title = '';
											if ( has_post_thumbnail() ) {
												$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
												$lesson_title  .= "<i style='background:url({$thumbnail_url})' class='tutor-lesson-thumbnail-icon $lesson_icon'></i>";
											} else {
												$lesson_title .= "<i class='$lesson_icon'></i>";
											}

											$countdown = '';
											if ( $post->post_type === 'tutor_zoom_meeting' ) {
												$lesson_title = '<i class="far fa-users"></i>';

												$zoom_meeting = tutor_zoom_meeting_data( $post->ID );
												$countdown    = '<div class="tutor-zoom-lesson-countdown tutor-lesson-duration" data-timer="' . $zoom_meeting->countdown_date . '" data-timezone="' . $zoom_meeting->timezone . '"></div>';
											}

											if ( $edumall_course->is_enrolled() || ( get_post_meta( $course_id, '_tutor_is_public_course', true ) === 'yes' && ! tutor_utils()->is_course_purchasable( $course_id ) ) ) {
												$lesson_title .= "<a href='" . get_the_permalink() . "'> " . get_the_title() . " </a>";

												$lesson_title .= $play_time ? "<span class='tutor-lesson-duration'>$play_time</span>" : '';

												if ( $countdown ) {
													if ( $zoom_meeting->is_expired ) {
														$lesson_title .= '<span class="tutor-zoom-label">' . __( 'Expired', 'edumall' ) . '</span>';
													} else if ( $zoom_meeting->is_started ) {
														$lesson_title .= '<span class="tutor-zoom-label tutor-zoom-live-label">' . __( 'Live', 'edumall' ) . '</span>';
													}
													$lesson_title .= $countdown;
												}

												echo '' . $lesson_title;
											} else {
												$lesson_title .= get_the_title();
												$lesson_title .= $play_time ? "<span class='tutor-lesson-duration'>$play_time</span>" : '';

												/**
												 * Can't remove plugin filter.
												 * Then used theme hook instead of.
												 */
												//echo apply_filters( 'tutor_course/contents/lesson/title', $lesson_title, get_the_ID() );
												echo apply_filters( 'edumall/tutor_course/contents/lesson/title', $lesson_title, get_the_ID() );
											}

											?>
										</h5>
									</div>

								<?php
								endwhile;
								$lessons->reset_postdata();
							endif;
							?>
						</div>
					</div>
				<?php
				endwhile;
				$topics->reset_postdata();
			endif;
			?>
		</div>
	</div>
<?php } ?>

<?php do_action( 'tutor_course/single/after/topics' ); ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php do_action( 'tutor_course/single/after/wrap' ); ?>

</div>
