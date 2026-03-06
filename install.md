# Installation Guide - WAMS Blog Notifier

Follow these steps to get WAMS Blog Notifier up and running on your WordPress site.

## 📥 Installation

1. **Upload the Plugin**:
    - Zip the `wams-blog-notifier` directory.
    - Go to your WordPress Admin Dashboard -> **Plugins** -> **Add New** -> **Upload Plugin**.
    - Choose the zip file and click **Install Now**.
    - Alternatively, upload the `wams-blog-notifier` folder directly to the `/wp-content/plugins/` directory via FTP/SFTP.

2. **Activate the Plugin**:
    - Once uploaded, click on **Activate Plugin** in the WordPress Plugins menu.

## ⚙️ Configuration

1. **Access Settings**:
    - Go to **WAMS Notifier** in the main sidebar menu.

2. **API Credentials**:
    - Obtain your API Key and Sender Number from the [Aztify Dashboard](https://wams.aztify.com).
    - Enter the **API Key** and **Sender Number** in the settings fields.

3. **Message Template**:
    - Customize the message that will be sent to subscribers. Use the available placeholders:
        - `{blog_name}`
        - `{post_title}`
        - `{post_url}`

4. **Test Connection**:
    - Click the **Test Connection** button to verify that your credentials are correct. You should receive a test message on your WhatsApp if the sender number is active.

5. **Display the Form**:
    - Add the shortcode `[wams_subscribe]` to any Page, Post, or Widget where you want users to subscribe.
