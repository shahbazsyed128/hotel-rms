# Hotel RMS (Restaurant Management System)

A PHP-based hotel and restaurant management system designed for local deployment and testing.

## ⚙️ Requirements

- **XAMPP** with PHP version **7.3**
- MySQL (included in XAMPP)
- Git (optional but recommended)
- Sublime Merge (optional GUI for Git)

## 📥 Installation Guide

### 1. Install XAMPP

Download and install XAMPP with PHP 7.3 from the link below:

👉 [Download XAMPP PHP 7.3](https://drive.google.com/file/d/1hFd2U1xzLXhau0PkBFCRtp2fX58cyi8i/view?usp=drive_link)

> ⚠️ Make sure to install XAMPP in the default directory (`C:\xampp`).

### 2. Clone the Repository

Navigate to the `htdocs` folder inside your XAMPP installation directory (usually `C:\xampp\htdocs`) and clone the repository:

```bash
cd C:\xampp\htdocs
git clone https://github.com/yourusername/hotel-rms.git
```

Alternatively, use [**Sublime Merge**](https://www.sublimemerge.com/) for a GUI-based Git experience.

### 3. Start XAMPP Services

Open the XAMPP Control Panel and **start Apache and MySQL**.

### 4. Database Setup

1. Go to `http://localhost/phpmyadmin`
2. Create a new database named:

```
hotelrms
```

> ⚠️ Do **not** use symbols or spaces in the database name due to installer validation.

### 5. Run the Installer

1. Switch to the installation branch:

```bash
git checkout installation-branch
```

2. Open your browser and navigate to:

```
http://localhost/hotel-rms/install/
```

3. Follow the on-screen steps:
   - Enter your database credentials.
   - Complete the installation.
   - Use **any key and ID** (this is a **nulled version** for testing only).
   - Remember your **username/email** and **password** for logging in later.

### 6. Final Steps

- After successful installation, **delete the `install` directory** from the project folder.
- Switch back to the `main` branch:

```bash
git checkout main
```

## ✅ You're Ready to Go!

You can now access and use the Hotel RMS system on your local server.

## 📝 Notes

- This version is for **testing and development purposes only**.
- Keep Apache and MySQL running while using the system.
- Secure your credentials appropriately if ever deployed outside a local environment.

## 🙌 Acknowledgments

Thanks to the original developers and contributors of Hotel RMS.
