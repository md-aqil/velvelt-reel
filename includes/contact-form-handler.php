<?php
/**
 * Contact Us Form Processing, Spam Protection & Auto-Acknowledgement Handler
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Process a contact form submission with spam protection, visitor acknowledgement, and admin alert.
 *
 * @param array $data Form submission fields
 * @return array Result with status, message, and reference ID
 */
function velvet_process_contact_submission($data) {
    // 1. Honeypot check (hidden field)
    if (!empty($data['velvet_hp_field'])) {
        return array(
            'success' => false,
            'message' => 'Spam detected. Submission blocked.',
            'code'    => 'honeypot_triggered',
        );
    }

    // 2. Time-gate verification (must take at least 2.5 seconds to fill out)
    if (isset($data['form_rendered_time'])) {
        $elapsed = time() - intval($data['form_rendered_time']);
        if ($elapsed < 2) {
            return array(
                'success' => false,
                'message' => 'Submission was too fast. Please try again.',
                'code'    => 'timegate_triggered',
            );
        }
    }

    // 3. Client IP and Rate Limiting
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'unknown';
    $rate_transient_key = 'velvet_contact_rate_' . md5($ip);
    $attempts = (int) get_transient($rate_transient_key);

    if ($attempts >= 5) {
        return array(
            'success' => false,
            'message' => 'Too many messages sent. Please wait a few minutes before trying again.',
            'code'    => 'rate_limited',
        );
    }

    // Increment rate counter (15-minute window)
    set_transient($rate_transient_key, $attempts + 1, 15 * MINUTE_IN_SECONDS);

    // 4. Sanitize and Validate Fields
    $name    = isset($data['name']) ? sanitize_text_field($data['name']) : '';
    $email   = isset($data['email']) ? sanitize_email($data['email']) : '';
    $phone   = isset($data['phone']) ? sanitize_text_field($data['phone']) : '';
    $subject = isset($data['subject']) ? sanitize_text_field($data['subject']) : 'General Inquiry';
    $message = isset($data['message']) ? wp_kses_post($data['message']) : '';

    if (empty($name)) {
        return array('success' => false, 'message' => 'Please provide your name.');
    }
    if (empty($email) || !is_email($email)) {
        return array('success' => false, 'message' => 'Please provide a valid email address.');
    }
    if (empty($message)) {
        return array('success' => false, 'message' => 'Please enter your message.');
    }

    // 5. Generate unique reference ID
    $reference_id    = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    $submission_time = date('M j, Y g:i A');

    // 6. Send Company-Admin Notification
    $designated_admin = get_option('velvet_contact_admin_email');
    if (empty($designated_admin)) {
        $designated_admin = get_option('admin_email');
    }
    $admin_recipient = apply_filters('velvet_contact_recipient_email', $designated_admin);

    $admin_args = array(
        'sender_name'     => $name,
        'sender_email'    => $email,
        'sender_phone'    => $phone,
        'sender_subject'  => $subject,
        'sender_message'  => $message,
        'submission_time' => $submission_time,
        'sender_ip'       => $ip,
        'reference_id'    => $reference_id,
    );

    $admin_html = velvet_render_email_template('email-contact-admin-alert.php', $admin_args);
    $admin_subj = '[Contact Us] ' . (!empty($subject) ? $subject : 'New Inquiry from ' . $name) . ' (#' . $reference_id . ')';

    $admin_headers = array(
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    $admin_sent = velvet_send_email($admin_recipient, $admin_subj, $admin_html, $admin_headers);

    // 7. Send Visitor Automatic Acknowledgement
    $visitor_args = array(
        'sender_name'     => $name,
        'sender_email'    => $email,
        'sender_phone'    => $phone,
        'sender_subject'  => $subject,
        'sender_message'  => $message,
        'submission_time' => $submission_time,
        'reference_id'    => $reference_id,
    );

    $visitor_html = velvet_render_email_template('email-contact-acknowledgement.php', $visitor_args);
    $visitor_subj = '✓ Message Received: ' . get_bloginfo('name') . ' (Ref: #' . $reference_id . ')';

    $visitor_sent = velvet_send_email($email, $visitor_subj, $visitor_html);

    // 8. Log Submission with Delivery Status
    $logs = get_option('velvet_contact_submissions_log', array());
    if (!is_array($logs)) {
        $logs = array();
    }

    $log_entry = array(
        'ref_id'       => $reference_id,
        'name'         => $name,
        'email'        => $email,
        'phone'        => $phone,
        'subject'      => $subject,
        'message'      => substr($message, 0, 300),
        'ip'           => $ip,
        'time'         => current_time('mysql'),
        'admin_sent'   => $admin_sent,
        'visitor_sent' => $visitor_sent,
    );

    array_unshift($logs, $log_entry);
    if (count($logs) > 50) {
        $logs = array_slice($logs, 0, 50);
    }
    update_option('velvet_contact_submissions_log', $logs);

    if (!$admin_sent && !$visitor_sent) {
        return array(
            'success' => false,
            'message' => 'There was an issue sending your message. Please try reaching us directly via phone or email.',
            'ref_id'  => $reference_id,
        );
    }

    return array(
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully. An acknowledgement email has been delivered to ' . esc_html($email) . '.',
        'ref_id'  => $reference_id,
    );
}

/**
 * AJAX Handler for VelvetReel Contact Us Form
 */
function velvet_ajax_submit_contact_form() {
    // Nonce verification
    if (!isset($_POST['velvet_contact_nonce']) || !wp_verify_nonce($_POST['velvet_contact_nonce'], 'velvet_contact_nonce_action')) {
        wp_send_json_error(array('message' => 'Security token expired. Please refresh the page.'));
        wp_die();
    }

    $result = velvet_process_contact_submission($_POST);

    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }

    wp_die();
}
add_action('wp_ajax_velvet_submit_contact_form', 'velvet_ajax_submit_contact_form');
add_action('wp_ajax_nopriv_velvet_submit_contact_form', 'velvet_ajax_submit_contact_form');

/**
 * Integration with Contact Form 7 (if installed)
 */
function velvet_cf7_mail_sent_acknowledgement($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();
        
        $name    = isset($posted_data['your-name']) ? $posted_data['your-name'] : (isset($posted_data['name']) ? $posted_data['name'] : '');
        $email   = isset($posted_data['your-email']) ? $posted_data['your-email'] : (isset($posted_data['email']) ? $posted_data['email'] : '');
        $subject = isset($posted_data['your-subject']) ? $posted_data['your-subject'] : (isset($posted_data['subject']) ? $posted_data['subject'] : 'Inquiry');
        $message = isset($posted_data['your-message']) ? $posted_data['your-message'] : (isset($posted_data['message']) ? $posted_data['message'] : '');
        $phone   = isset($posted_data['your-phone']) ? $posted_data['your-phone'] : (isset($posted_data['phone']) ? $posted_data['phone'] : '');

        if (!empty($email) && is_email($email)) {
            $visitor_args = array(
                'sender_name'     => $name,
                'sender_email'    => $email,
                'sender_phone'    => $phone,
                'sender_subject'  => $subject,
                'sender_message'  => $message,
                'submission_time' => date('M j, Y g:i A'),
                'reference_id'    => strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
            );

            $visitor_html = velvet_render_email_template('email-contact-acknowledgement.php', $visitor_args);
            $visitor_subj = '✓ Message Received: ' . get_bloginfo('name');
            velvet_send_email($email, $visitor_subj, $visitor_html);
        }
    }
}
add_action('wpcf7_mail_sent', 'velvet_cf7_mail_sent_acknowledgement');

/**
 * Integration with Elementor Pro Forms (if installed)
 */
function velvet_elementor_form_acknowledgement($record, $handler) {
    $form_data = $record->get_formatted_data();
    
    $name    = isset($form_data['Name']) ? $form_data['Name'] : (isset($form_data['name']) ? $form_data['name'] : '');
    $email   = isset($form_data['Email']) ? $form_data['Email'] : (isset($form_data['email']) ? $form_data['email'] : '');
    $message = isset($form_data['Message']) ? $form_data['Message'] : (isset($form_data['message']) ? $form_data['message'] : '');
    $phone   = isset($form_data['Phone']) ? $form_data['Phone'] : (isset($form_data['phone']) ? $form_data['phone'] : '');
    $subject = isset($form_data['Subject']) ? $form_data['Subject'] : 'Website Inquiry';

    if (!empty($email) && is_email($email)) {
        $visitor_args = array(
            'sender_name'     => $name,
            'sender_email'    => $email,
            'sender_phone'    => $phone,
            'sender_subject'  => $subject,
            'sender_message'  => $message,
            'submission_time' => date('M j, Y g:i A'),
            'reference_id'    => strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
        );

        $visitor_html = velvet_render_email_template('email-contact-acknowledgement.php', $visitor_args);
        $visitor_subj = '✓ Message Received: ' . get_bloginfo('name');
        velvet_send_email($email, $visitor_subj, $visitor_html);
    }
}
add_action('elementor_pro/forms/new_record', 'velvet_elementor_form_acknowledgement', 10, 2);

/**
 * Render standard Contact Us Form Shortcode: [velvet_contact_form]
 */
function velvet_contact_form_shortcode($atts) {
    $rendered_time = time();
    $nonce = wp_create_nonce('velvet_contact_nonce_action');

    ob_start();
    ?>
    <div class="velvet-contact-container" id="velvetContactWrapper">
        <form id="velvetContactForm" class="velvet-contact-form" method="post">
            <input type="hidden" name="action" value="velvet_submit_contact_form">
            <input type="hidden" name="velvet_contact_nonce" value="<?php echo esc_attr($nonce); ?>">
            <input type="hidden" name="form_rendered_time" value="<?php echo esc_attr($rendered_time); ?>">
            
            <!-- Anti-Spam Honeypot Field -->
            <div style="display:none !important; visibility:hidden !important; opacity:0 !important; position:absolute !important; left:-9999px !important;">
                <label for="velvet_hp_field">Leave this field empty</label>
                <input type="text" name="velvet_hp_field" id="velvet_hp_field" tabindex="-1" autocomplete="off">
            </div>

            <div id="velvetContactAlert" class="velvet-contact-alert" style="display:none;"></div>

            <div class="velvet-form-row" style="display: flex; gap: 16px; flex-wrap: wrap;">
                <div class="velvet-form-group" style="flex: 1; min-width: 220px; margin-bottom: 16px;">
                    <label class="velvet-form-label" for="contact_name">Full Name <span style="color: #FE114B;">*</span></label>
                    <input type="text" id="contact_name" name="name" class="velvet-input" placeholder="Your Name" required>
                </div>
                <div class="velvet-form-group" style="flex: 1; min-width: 220px; margin-bottom: 16px;">
                    <label class="velvet-form-label" for="contact_email">Email Address <span style="color: #FE114B;">*</span></label>
                    <input type="email" id="contact_email" name="email" class="velvet-input" placeholder="you@domain.com" required>
                </div>
            </div>

            <div class="velvet-form-row" style="display: flex; gap: 16px; flex-wrap: wrap;">
                <div class="velvet-form-group" style="flex: 1; min-width: 220px; margin-bottom: 16px;">
                    <label class="velvet-form-label" for="contact_phone">Phone / WhatsApp</label>
                    <input type="tel" id="contact_phone" name="phone" class="velvet-input" placeholder="+1 (555) 000-0000 (Optional)">
                </div>
                <div class="velvet-form-group" style="flex: 1; min-width: 220px; margin-bottom: 16px;">
                    <label class="velvet-form-label" for="contact_subject">Subject</label>
                    <input type="text" id="contact_subject" name="subject" class="velvet-input" placeholder="e.g. Casting Inquiry, Collaboration, Support">
                </div>
            </div>

            <div class="velvet-form-group" style="margin-bottom: 20px;">
                <label class="velvet-form-label" for="contact_message">Your Message <span style="color: #FE114B;">*</span></label>
                <textarea id="contact_message" name="message" class="velvet-textarea" rows="5" placeholder="How can we assist you?" required></textarea>
            </div>

            <div class="velvet-form-actions">
                <button type="submit" id="velvetContactSubmitBtn" class="velvet-btn-primary" style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                    <span>Send Message</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var contactForm = document.getElementById('velvetContactForm');
        var alertBox = document.getElementById('velvetContactAlert');
        var submitBtn = document.getElementById('velvetContactSubmitBtn');

        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var originalBtnHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Sending...</span>';

                if (alertBox) {
                    alertBox.style.display = 'none';
                    alertBox.className = 'velvet-contact-alert';
                }

                var formData = new FormData(contactForm);

                fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;

                    if (alertBox) {
                        alertBox.style.display = 'block';
                        if (data.success) {
                            alertBox.style.padding = '14px 18px';
                            alertBox.style.background = 'rgba(34, 197, 94, 0.1)';
                            alertBox.style.border = '1px solid rgba(34, 197, 94, 0.3)';
                            alertBox.style.color = '#22c55e';
                            alertBox.style.borderRadius = '8px';
                            alertBox.style.marginBottom = '20px';
                            alertBox.innerHTML = '<strong>Success:</strong> ' + data.data.message;
                            contactForm.reset();
                        } else {
                            alertBox.style.padding = '14px 18px';
                            alertBox.style.background = 'rgba(239, 68, 68, 0.1)';
                            alertBox.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                            alertBox.style.color = '#ef4444';
                            alertBox.style.borderRadius = '8px';
                            alertBox.style.marginBottom = '20px';
                            alertBox.innerHTML = '<strong>Error:</strong> ' + (data.data && data.data.message ? data.data.message : 'Failed to send message. Please try again.');
                        }
                    }
                })
                .catch(function(err) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    if (alertBox) {
                        alertBox.style.display = 'block';
                        alertBox.style.padding = '14px 18px';
                        alertBox.style.background = 'rgba(239, 68, 68, 0.1)';
                        alertBox.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                        alertBox.style.color = '#ef4444';
                        alertBox.style.borderRadius = '8px';
                        alertBox.style.marginBottom = '20px';
                        alertBox.innerHTML = '<strong>Network Error:</strong> Unable to connect. Please try again.';
                    }
                });
            });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('velvet_contact_form', 'velvet_contact_form_shortcode');
