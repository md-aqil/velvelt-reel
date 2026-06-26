<?php
/**
 * Template Name: User Profile
 */
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$first_name = $current_user->first_name;
$last_name = $current_user->last_name;
$email = $current_user->user_email;
$username = $current_user->user_login;

global $wpdb;
$table = $wpdb->prefix . 'userinformation';
$extra = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id), ARRAY_A);

$dob = $extra['dob'] ?? '';
$address = $extra['address'] ?? '';
$region = $extra['region'] ?? '';
$phone = $extra['phone'] ?? '';
$street_address = $extra['street_address'] ?? '';
$zip_code = $extra['zip_code'] ?? '';

$profile_errors = [];
$password_errors = [];
$profile_success = false;
$password_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_first = sanitize_text_field($_POST['first_name']);
    $new_last = sanitize_text_field($_POST['last_name']);
    $new_dob = sanitize_text_field($_POST['dob']);
    $new_email = sanitize_email($_POST['email']);
    $new_phone = sanitize_text_field($_POST['phone']);
    $new_address = sanitize_text_field($_POST['address']);
    $new_region = sanitize_text_field($_POST['region']);
    $new_street = sanitize_text_field($_POST['street_address']);
    $new_zip = sanitize_text_field($_POST['zip_code']);

    if (!$new_first) $profile_errors[] = "First name is required.";
    if (!$new_last) $profile_errors[] = "Last name is required.";
    if (!$new_email || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) $profile_errors[] = "Valid email is required.";

    if (empty($profile_errors)) {
        // Check if email is changed and already exists
        if ($new_email !== $email && email_exists($new_email)) {
            $profile_errors[] = "This email is already in use.";
        } else {
            wp_update_user([
                'ID' => $user_id,
                'user_email' => $new_email,
                'first_name' => $new_first,
                'last_name' => $new_last
            ]);

            $data = [
                'dob' => $new_dob,
                'phone' => $new_phone,
                'address' => $new_address,
                'region' => $new_region,
                'street_address' => $new_street,
                'zip_code' => $new_zip
            ];

            if ($extra) {
                $wpdb->update($table, $data, ['user_id' => $user_id]);
            } else {
                $data['user_id'] = $user_id;
                $wpdb->insert($table, $data);
            }

            $profile_success = true;

            // Refresh data
            $first_name = $new_first;
            $last_name = $new_last;
            $email = $new_email;
            $dob = $new_dob;
            $phone = $new_phone;
            $address = $new_address;
            $region = $new_region;
            $street_address = $new_street;
            $zip_code = $new_zip;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$current_password) $password_errors[] = "Current password is required.";
    if (!$new_password) {
        $password_errors[] = "New password is required.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $new_password)) {
        $password_errors[] = "Password must be at least 6 characters with uppercase, lowercase, number, and special character.";
    }
    if ($new_password !== $confirm_password) $password_errors[] = "New passwords do not match.";

    if (empty($password_errors)) {
        if (!wp_check_password($current_password, $current_user->user_pass, $user_id)) {
            $password_errors[] = "Current password is incorrect.";
        } else {
            wp_set_password($new_password, $user_id);
            $password_success = true;
            // Re-authenticate user after password change
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id, true);
        }
    }
}
?>

<style>
.dashboard-body {
    background: linear-gradient(0deg, #6b0f0f, #2d2d2d);
    background-size: 400% 400%;
    animation: rotateGradient 10s linear infinite;
    min-height: 100vh;
    color: white;
    padding: 20px;
    margin-top: -102px;
    padding-top: 130px;
}

@keyframes rotateGradient {
    0% { background: linear-gradient(0deg, #6b0f0f, #2d2d2d); }
    25% { background: linear-gradient(90deg, #6b0f0f, #2d2d2d); }
    50% { background: linear-gradient(180deg, #6b0f0f, #2d2d2d); }
    75% { background: linear-gradient(270deg, #6b0f0f, #2d2d2d); }
    100% { background: linear-gradient(360deg, #6b0f0f, #2d2d2d); }
}

.dashboard-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 40px;
    background: #0a0a0a;
    border-radius: 20px;
    border: 1px solid #1a1a1a;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
}

.dashboard-header {
    text-align: center;
    margin-bottom: 30px;
}

.dashboard-header h2 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 5px;
}

.dashboard-header p {
    color: #888;
    font-size: 0.95rem;
}

.dashboard-section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #1a1a1a;
}

.dashboard-success-message {
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 20px;
    color: #4ade80;
    text-align: center;
}

.dashboard-error-messages {
    background: rgba(178, 18, 45, 0.15);
    border: 1px solid rgba(178, 18, 45, 0.3);
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 20px;
}

.dashboard-error-messages ul {
    list-style: none;
}

.dashboard-error-messages li {
    color: #fff;
    margin-bottom: 6px;
    font-size: 0.95rem;
    padding-left: 20px;
    position: relative;
}

.dashboard-error-messages li:before {
    content: "\26A0";
    position: absolute;
    left: 0;
    color: #dc3545;
}

.dashboard-form {
    display: grid;
    gap: 20px;
    margin-bottom: 40px;
}

.dashboard-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.dashboard-form-group {
    display: flex;
    flex-direction: column;
}

.dashboard-label {
    font-size: 0.95rem;
    font-weight: 500;
    color: #e0e0e0;
    margin-bottom: 8px;
    letter-spacing: 0.3px;
}

.dashboard-input {
    background: #0a0a0a;
    border: 1px solid #222;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 1rem;
    color: #ffffff;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.dashboard-input::placeholder {
    color: #555;
}

.dashboard-input:focus {
    outline: none;
    border-color: #b2122d;
    background: #111;
    box-shadow: 0 0 0 3px rgba(178, 18, 45, 0.1);
    transform: translateY(-1px);
}

.dashboard-submit-btn {
    background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 16px 40px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    font-family: 'Inter', sans-serif;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    width: 100%;
}

.dashboard-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(178, 18, 45, 0.4);
    background: linear-gradient(135deg, #df1d3d 0%, #ff4d6d 100%);
}

.dashboard-divider {
    border: none;
    border-top: 1px solid #1a1a1a;
    margin: 30px 0;
}

@media (max-width: 768px) {
    .dashboard-container {
        margin: 10px;
        padding: 30px 25px;
    }

    .dashboard-form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}
</style>

<div class="dashboard-body">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Welcome, <?= esc_html($first_name ?: $username) ?></h2>
            <p>Manage your profile information</p>
        </div>

        <!-- Profile Info Section -->
        <h3 class="dashboard-section-title">Profile Information</h3>

        <?php if ($profile_success): ?>
        <div class="dashboard-success-message">
            Profile updated successfully.
        </div>
        <?php endif; ?>

        <?php if (!empty($profile_errors)): ?>
        <div class="dashboard-error-messages">
            <ul>
                <?php foreach ($profile_errors as $err): ?>
                <li><?= esc_html($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" class="dashboard-form">
            <div class="dashboard-form-row">
                <div class="dashboard-form-group">
                    <label class="dashboard-label">First Name</label>
                    <input type="text" name="first_name" value="<?= esc_attr($first_name) ?>" class="dashboard-input" placeholder="Enter first name">
                </div>

                <div class="dashboard-form-group">
                    <label class="dashboard-label">Last Name</label>
                    <input type="text" name="last_name" value="<?= esc_attr($last_name) ?>" class="dashboard-input" placeholder="Enter last name">
                </div>
            </div>

            <div class="dashboard-form-row">
                <div class="dashboard-form-group">
                    <label class="dashboard-label">Date of Birth</label>
                    <input type="date" name="dob" value="<?= esc_attr($dob) ?>" class="dashboard-input">
                </div>

                <div class="dashboard-form-group">
                    <label class="dashboard-label">Email</label>
                    <input type="email" name="email" value="<?= esc_attr($email) ?>" class="dashboard-input" placeholder="Enter email">
                </div>
            </div>

            <div class="dashboard-form-group">
                <label class="dashboard-label">Phone</label>
                <input type="text" name="phone" value="<?= esc_attr($phone) ?>" class="dashboard-input" placeholder="Enter phone number">
            </div>

            <div class="dashboard-form-group">
                <label class="dashboard-label">Address</label>
                <input type="text" name="address" value="<?= esc_attr($address) ?>" class="dashboard-input" placeholder="Enter address">
            </div>

            <div class="dashboard-form-row">
                <div class="dashboard-form-group">
                    <label class="dashboard-label">Region</label>
                    <input type="text" name="region" value="<?= esc_attr($region) ?>" class="dashboard-input" placeholder="Enter region">
                </div>

                <div class="dashboard-form-group">
                    <label class="dashboard-label">Street Address</label>
                    <input type="text" name="street_address" value="<?= esc_attr($street_address) ?>" class="dashboard-input" placeholder="Enter street address">
                </div>
            </div>

            <div class="dashboard-form-group">
                <label class="dashboard-label">ZIP Code</label>
                <input type="text" name="zip_code" value="<?= esc_attr($zip_code) ?>" class="dashboard-input" placeholder="Enter ZIP code">
            </div>

            <button type="submit" name="update_profile" class="dashboard-submit-btn">Update Profile</button>
        </form>

        <hr class="dashboard-divider">

        <!-- Change Password Section -->
        <h3 class="dashboard-section-title">Change Password</h3>

        <?php if ($password_success): ?>
        <div class="dashboard-success-message">
            Password changed successfully.
        </div>
        <?php endif; ?>

        <?php if (!empty($password_errors)): ?>
        <div class="dashboard-error-messages">
            <ul>
                <?php foreach ($password_errors as $err): ?>
                <li><?= esc_html($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" class="dashboard-form">
            <div class="dashboard-form-group">
                <label class="dashboard-label">Current Password</label>
                <input type="password" name="current_password" class="dashboard-input" placeholder="Enter current password">
            </div>

            <div class="dashboard-form-row">
                <div class="dashboard-form-group">
                    <label class="dashboard-label">New Password</label>
                    <input type="password" name="new_password" class="dashboard-input" placeholder="Enter new password">
                </div>

                <div class="dashboard-form-group">
                    <label class="dashboard-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="dashboard-input" placeholder="Confirm new password">
                </div>
            </div>

            <button type="submit" name="change_password" class="dashboard-submit-btn">Change Password</button>
        </form>
    </div>
</div>

<?php get_footer(); ?>
