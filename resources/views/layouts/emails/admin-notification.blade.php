<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Kemitraan Baru</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold;">🔔 Pengajuan Kemitraan Baru</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 20px 0; color: #1f2937; font-size: 16px;">
                                Ada pengajuan kemitraan baru yang perlu ditinjau:
                            </p>
                            
                            <table width="100%" cellpadding="8" cellspacing="0" style="border: 1px solid #e5e7eb; border-radius: 4px;">
                                <tr style="background-color: #f9fafb;">
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: bold;">
                                        Nama Lengkap (KTP)
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px;">
                                        {{ $mitra->nama_lengkap_ktp }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: bold; background-color: #f9fafb;">
                                        NIK
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px;">
                                        {{ $mitra->nik }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: bold; background-color: #f9fafb;">
                                        Email
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px;">
                                        <a href="mailto:{{ $mitra->alamat_email }}" style="color: #3b82f6; text-decoration: none;">
                                            {{ $mitra->alamat_email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: bold; background-color: #f9fafb;">
                                        Alamat Properti
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px;">
                                        {{ $mitra->alamat_properti }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: bold; background-color: #f9fafb;">
                                        Foto Properti
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px;">
                                        <a href="{{ asset('storage/' . $mitra->foto_properti_path) }}" style="color: #3b82f6; text-decoration: none;">
                                            Lihat Foto →
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; color: #6b7280; font-size: 14px; font-weight: bold; background-color: #f9fafb;">
                                        Status
                                    </td>
                                    <td style="padding: 12px; color: #1f2937; font-size: 14px;">
                                        <span style="background-color: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                                            {{ strtoupper($mitra->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            
                            <div style="margin-top: 25px; padding: 15px; background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 4px;">
                                <p style="margin: 0; color: #1e40af; font-size: 14px;">
                                    <strong>Tindakan:</strong> Silakan review pengajuan ini dan hubungi calon mitra untuk proses selanjutnya.
                                </p>
                            </div>
                            
                            <p style="margin: 25px 0 0 0; color: #6b7280; font-size: 12px;">
                                Pengajuan diterima pada: {{ $mitra->created_at->format('d F Y, H:i') }} WIB
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #6b7280; font-size: 12px;">
                                Email otomatis dari sistem UrbanOffice
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>