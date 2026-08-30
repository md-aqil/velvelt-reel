<?php
/**
 * VelvetReel Branded Email Footer Template
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name       = get_bloginfo('name');
$site_url        = home_url('/');
$unsubscribe_url = isset($unsubscribe_url) ? $unsubscribe_url : '';
$profile_url     = home_url('/dashboard/');
$current_year    = date('Y');
?>
                        </td>
                    </tr>
                    
                    <!-- Email Footer -->
                    <tr>
                        <td bgcolor="#0d0d10" style="padding: 24px 36px 32px 36px; background-color: #0d0d10; background: #0d0d10; background-image: linear-gradient(#0d0d10, #0d0d10); border-top: 1px solid #1f1f26; text-align: center;" class="mobile-padding">
                            <p style="margin: 0 0 10px 0; font-size: 13px; color: #a1a1aa; line-height: 1.5;">
                                Sent with ❤️ by <a href="<?php echo esc_url($site_url); ?>" style="color: #ffffff; font-weight: 600; text-decoration: none;"><?php echo esc_html($site_name); ?></a> &bull; Discover and be discovered.
                            </p>
                            
                            <p style="margin: 0 0 14px 0; font-size: 11px; color: #71717a; line-height: 1.6;">
                                You are receiving this notification because you have an account or active profile on VelvetReel.
                                <?php if (!empty($unsubscribe_url)) : ?>
                                    <br>Prefer not to receive these emails? <a href="<?php echo esc_url($unsubscribe_url); ?>" style="color: #DF1D3D; text-decoration: underline;">Unsubscribe from alerts</a> or manage preferences in your <a href="<?php echo esc_url($profile_url); ?>" style="color: #ffffff; text-decoration: underline;">Dashboard</a>.
                                <?php endif; ?>
                            </p>

                            <p style="margin: 0 0 4px 0; font-size: 11px; color: #71717a; line-height: 1.5;">
                                <strong>VelvetReel Entertainment Network</strong> &bull; Mumbai, India &bull; <a href="mailto:thevelvetreelproductions@gmail.com" style="color: #a1a1aa; text-decoration: underline;">thevelvetreelproductions@gmail.com</a>
                            </p>

                            <p style="margin: 0; font-size: 10px; color: #52525b;">
                                &copy; <?php echo esc_html($current_year); ?> <?php echo esc_html($site_name); ?>. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- End Email Container Box -->

            </td>
        </tr>
    </table>
</body>
</html>
