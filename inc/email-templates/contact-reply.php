<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>We received your request</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #000000; color: #ffffff;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0; text-align: center; background: radial-gradient(circle at 50% 50%, #1D2144 0%, #090E34 100%);">
                <div style="max-width: 600px; margin: 0 auto; background-color: #1F2024; border: 1px solid #2E3038; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <!-- Header with Logo -->
                    <div style="background-color: #1F2024; padding: 30px 20px; text-align: center; border-bottom: 1px solid #2E3038;">
                        <img src="cid:company-logo" alt="<?php echo get_bloginfo('name'); ?>" style="max-width: 150px; height: auto;">
                    </div>
                    
                    <!-- Content -->
                    <div style="padding: 40px 30px; text-align: left; color: #d1d5db; background-color: #060607;">
                        <h1 style="color: #ffffff; margin-top: 0; margin-bottom: 20px; font-size: 24px;">Message Received!</h1>
                        
                        <p style="font-size: 16px; margin-bottom: 20px; color: #ffffff;">Hello <strong><?php echo esc_html($name); ?></strong>,</p>
                        
                        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                            Thanks for reaching out to us! We have successfully received your message and a ticket has been created.
                        </p>
                        
                        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                            Our support team is reviewing your request and will get back to you as soon as possible with a detailed response.
                        </p>
                        
                        <div style="background-color: #242B51; border-left: 4px solid #4A6CF7; padding: 15px; margin-bottom: 30px; border-radius: 4px;">
                            <p style="margin: 0; font-size: 14px; color: #9ca3af;">
                                <strong style="color: #ffffff;">Reference:</strong> We will contact you at <span style="color: #4A6CF7;"><?php echo esc_html($email); ?></span>
                            </p>
                        </div>
                        
                        <p style="font-size: 16px; margin-bottom: 0;">
                            Best regards,<br>
                            <strong style="color: #ffffff;"><?php echo get_bloginfo('name'); ?> Team</strong>
                        </p>
                    </div>
                    
                    <!-- Footer -->
                    <div style="background-color: #18191E; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border-top: 1px solid #2E3038;">
                        <!-- Social Links -->
                        <div style="margin-bottom: 20px;">
                            <a href="#" style="color: #d1d5db; text-decoration: none; margin: 0 10px; font-size: 14px;">Facebook</a>
                            <span style="color: #4A6CF7;">&bull;</span>
                            <a href="#" style="color: #d1d5db; text-decoration: none; margin: 0 10px; font-size: 14px;">Twitter</a>
                            <span style="color: #4A6CF7;">&bull;</span>
                            <a href="#" style="color: #d1d5db; text-decoration: none; margin: 0 10px; font-size: 14px;">YouTube</a>
                            <span style="color: #4A6CF7;">&bull;</span>
                            <a href="#" style="color: #d1d5db; text-decoration: none; margin: 0 10px; font-size: 14px;">LinkedIn</a>
                        </div>
                        
                        <p style="margin: 0;">&copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. All rights reserved.</p>
                        <p style="margin: 10px 0 0;"><a href="<?php echo home_url(); ?>" style="color: #4A6CF7; text-decoration: none;"><?php echo home_url(); ?></a></p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
