<!DOCTYPE html>
<html>
<head>
    <title>Penerimaan Pegawai - Almahira</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #28a745;">Selamat Bergabung!</h2>
        </div>
        
        <p>Halo <strong>{{ $user->name }}</strong>,</p>
        
        <p>Selamat! Lamaran Anda telah diterima dan Anda resmi terdaftar sebagai bagian dari tim kami. Akun sistem Anda telah berhasil dibuat.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0 0 10px 0;">Berikut adalah detail login Anda:</p>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 30%;"><strong>Email</strong></td>
                    <td>: {{ $user->email }}</td>
                </tr>
                <tr>
                    <td><strong>Password</strong></td>
                    <td>: <code>{{ $password }}</code></td>
                </tr>
                <tr>
                    <td><strong>Role Akses</strong></td>
                    <td>: {{ $roleName }}</td>
                </tr>
            </table>
        </div>
        
        <p style="color: #d9534f; font-size: 0.9em;">
            <em>Mohon untuk segera mengganti password Anda setelah login pertama kali demi keamanan akun Anda.</em>
        </p>
        
        <p>Silakan login ke sistem melalui tautan berikut:</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/login') }}" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Login ke Sistem</a>
        </p>
        
        <p>Terima kasih,<br>Tim Manajemen Almahira</p>
    </div>
</body>
</html>
