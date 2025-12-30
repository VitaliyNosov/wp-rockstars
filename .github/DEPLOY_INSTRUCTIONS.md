# CI/CD Guide

We have successfully configured and tested the CI/CD system for your project.

## Status: ✅ Configured and Tested
-   **CI (Continuous Integration)**: Works automatically. GitHub checks the build of the theme and application on every code update. You can see green checkmarks in the "Actions" tab.
-   **CD (Continuous Deployment)**: The script is created and ready to work, awaiting servers.

## What to do next (when servers are available)

When you purchase and configure a server (VPS), you will need to perform the following steps to activate the "Deploy" button:

### 1. Prepare the Server
Ensure that `rsync` is installed on the server and the folders for the site are created:
-   `/var/www/html/wp-content/themes/rock-stars` (for the theme)
-   `/var/www/nextjs-app` (for the application)

### 2. Add Secrets to GitHub
Go to **Settings** -> **Secrets and variables** -> **Actions** in your repository and add:

| Secret Name | Value |
| :--- | :--- |
| `SERVER_IP` | Your server's IP address (e.g., `192.168.1.1`) |
| `SERVER_USER` | Username (e.g., `root` or `ubuntu`) |
| `SSH_PRIVATE_KEY` | Your private SSH key (content of .pem or id_rsa file) |

### 3. Run Deploy
1.  Go to the **Actions** tab.
2.  Select **"Deploy to Production"**.
3.  Click **Run workflow**.
