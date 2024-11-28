<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    // Index: Menampilkan daftar sertifikat
    public function index()
    {
        $certificates = Certificate::all();
        return view('certificates.index', compact('certificates'));
    }

    // Create: Form untuk menambah sertifikat baru
    public function create()
    {
        return view('certificates.create');
    }

    // Store: Proses simpan sertifikat ke database
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:certificates',
        'photo_profile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'position' => 'required|string|max:255',
    ]);

    // Handle Photo Profile Upload
    if ($request->hasFile('photo_profile')) {
        $photoProfile = $request->file('photo_profile');
        $photoProfilePath = $photoProfile->store('photo_profiles', 'public'); // Simpan di folder storage/app/public/photo_profiles
    }

    // Generate Certificate Number
    $certificateNumber = 'CERT-' . strtoupper(uniqid());

    // Generate QR Code Data (URL to Certificate Detail)
    $qrCodeData = route('certificates.show', $certificateNumber);

    // Path to Save QR Code
    $qrCodePath = 'qrcodes/' . $certificateNumber . '.png';

    // Generate QR Code and Save
    QrCode::format('png')->size(300)->generate($qrCodeData, public_path('storage/' . $qrCodePath));

    // Save Certificate to Database
    Certificate::create([
        'name' => $request->name,
        'email' => $request->email,
        'position' => $request->position,
        'certificate_number' => $certificateNumber,
        'qr_code_path' => 'storage/' . $qrCodePath,
        'photo_profile' => 'storage/' . $photoProfilePath,
    ]);

    return redirect()->route('certificates.index')->with('success', 'Certificate created successfully!');
}


    // Show: Menampilkan detail sertifikat
    public function show($certificateNumber)
    {
        $certificate = Certificate::where('certificate_number', $certificateNumber)->firstOrFail();
        return view('certificates.show', compact('certificate'));
    }

      // Show: Menampilkan detail sertifikat
    //   public function show($certificateNumber)
    //   {
    //       // Mendapatkan data sertifikat berdasarkan nomor sertifikat
    //       $certificate = Certificate::where('certificate_number', $certificateNumber)->firstOrFail();
  
    //       // URL ngrok
    //       $ngrokUrl = 'https://ee59-2001-448a-4003-27d4-598d-3e-b337-7500.ngrok-free.app';
  
    //       // URL lengkap sertifikat yang mengarah ke halaman detail sertifikat
    //       $certificateUrl = $ngrokUrl . '/certificate/' . $certificate->certificate_number;
  
    //       // Generate QR Code
    //       $qrCode = QrCode::size(300)->generate($certificateUrl);
  
    //       // Mengirim data ke view
    //       return view('certificates.show', compact('certificate', 'qrCode'));
    //   }

public function downloadQrCode($certificateNumber)
{
    $certificate = Certificate::where('certificate_number', $certificateNumber)->firstOrFail();

    // Path to QR Code
    $filePath = public_path($certificate->qr_code_path);

    // Check if file exists
    if (!file_exists($filePath)) {
        abort(404, 'QR Code not found.');
    }

    // Return file download response
    return response()->download($filePath, $certificate->certificate_number . '-qr-code.png');
}

    
}
