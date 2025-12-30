# 🎸 Rock Stars WordPress Theme & Headless App

> A modern WordPress theme with an integrated Headless Next.js application.
> Combining the power of WordPress CMS with the speed of React.

![Project Status](https://img.shields.io/badge/Status-Active_Development-success?style=for-the-badge)
![License](https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge)

## 🛠 Tech Stack

<p align="center">
  <img src="https://img.shields.io/badge/WordPress-21759B?style=for-the-badge&logo=wordpress&logoColor=white" alt="WordPress" />
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/Next.js-black?style=for-the-badge&logo=next.js&logoColor=white" alt="Next.js" />
  <img src="https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB" alt="React" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind" />
  <img src="https://img.shields.io/badge/Sass-CC6699?style=for-the-badge&logo=sass&logoColor=white" alt="Sass" />
  <img src="https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white" alt="Composer" />
  <img src="https://img.shields.io/badge/GitHub_Actions-2088FF?style=for-the-badge&logo=github-actions&logoColor=white" alt="CI/CD" />
</p>

## 📂 Project Structure

The project consists of two main parts:

1.  **WordPress Theme** (Repository Root)
    *   Classic WordPress theme.
    *   Handlers API (GraphQL), Admin Panel, and basic templates.
    *   Uses `Composer` for PHP dependency management (Carbon Fields, etc.).

2.  **Headless App** (`/rock-star` directory)
    *   Frontend application built on Next.js.
    *   Fetches data from WordPress via WPGraphQL.
    *   Uses `Tailwind CSS` for styling.

## 🚀 CI/CD Workflow

Automation is configured via **GitHub Actions**.

| Branch | Action | Description |
| :--- | :--- | :--- |
| `main` | **Development** | Main development branch. Pushing here **does not trigger** deployment. |
| `deploy` | **Production** | Release branch. Pushing here triggers **Build (CI)** and **Deploy (CD)** to production. |

### How to Release
1.  Switch to the `deploy` branch.
2.  Merge changes from `main`.
3.  Push to GitHub.

```bash
git checkout deploy
git merge main
git push origin deploy
```

## 📦 Installation & Setup

### WordPress Theme
```bash
composer install
```

### Next.js App
```bash
cd rock-star
npm install
npm run dev
```

---
*Developed by Rock Stars Team* 🎸
