'use client';

import Navbar from '@/components/Navbar';
import { Building2, Users, FileCheck, Shield, MapPin, Clock } from 'lucide-react';

export default function Home() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />

      {/* Hero Section */}
      <section className="bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700 text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-5xl font-bold mb-4">
              Selamat Datang di Website Resmi
            </h1>
            <h2 className="text-3xl md:text-4xl font-bold mb-6">
              Desa Kasomalang Kulon
            </h2>
            <p className="text-lg md:text-xl text-primary-100 max-w-2xl mx-auto mb-8">
              Portal informasi desa yang transparan, modern, dan profesional untuk masyarakat Kabupaten Subang
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <a href="/pbb" className="px-6 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-primary-50 transition">
                Cek Status PBB
              </a>
              <a href="/bansos/cek" className="px-6 py-3 bg-primary-800 text-white font-semibold rounded-lg hover:bg-primary-900 transition">
                Cek Bansos
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Jam Pelayanan */}
      <section className="bg-white shadow-sm -mt-8 relative z-10 max-w-5xl mx-auto rounded-lg">
        <div className="p-6">
          <div className="flex items-center justify-center space-x-2 text-gray-600">
            <Clock className="w-5 h-5" />
            <span className="font-medium">Jam Pelayanan: Senin - Jumat, 08:00 - 16:00 WIB</span>
          </div>
        </div>
      </section>

      {/* Statistik Penduduk Ringkas */}
      <section className="py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-bold text-gray-800 mb-6 text-center">Statistik Penduduk</h2>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div className="bg-white p-6 rounded-lg shadow-md text-center">
              <Users className="w-8 h-8 text-primary-600 mx-auto mb-2" />
              <p className="text-3xl font-bold text-gray-800">5,234</p>
              <p className="text-sm text-gray-500">Total Penduduk</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md text-center">
              <Users className="w-8 h-8 text-blue-600 mx-auto mb-2" />
              <p className="text-3xl font-bold text-gray-800">2,612</p>
              <p className="text-sm text-gray-500">Laki-laki</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md text-center">
              <Users className="w-8 h-8 text-pink-600 mx-auto mb-2" />
              <p className="text-3xl font-bold text-gray-800">2,622</p>
              <p className="text-sm text-gray-500">Perempuan</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md text-center">
              <Building2 className="w-8 h-8 text-green-600 mx-auto mb-2" />
              <p className="text-3xl font-bold text-gray-800">1,456</p>
              <p className="text-sm text-gray-500">Kepala Keluarga</p>
            </div>
          </div>
        </div>
      </section>

      {/* Widget PBB & Bansos */}
      <section className="py-8 bg-gray-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-2 gap-6">
            {/* Kartu PBB */}
            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center space-x-3 mb-4">
                <FileCheck className="w-8 h-8 text-primary-600" />
                <h3 className="text-xl font-bold text-gray-800">Pembayaran PBB</h3>
              </div>
              <div className="mb-4">
                <div className="flex justify-between text-sm mb-1">
                  <span className="text-gray-600">Realisasi Tahun 2024</span>
                  <span className="font-semibold text-primary-600">78%</span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-3">
                  <div className="bg-primary-600 h-3 rounded-full" style={{ width: '78%' }}></div>
                </div>
              </div>
              <p className="text-sm text-gray-600 mb-4">
                Total wajib pajak: 1,234 | Sudah lunas: 962 | Belum bayar: 272
              </p>
              <a href="/pbb" className="inline-block w-full text-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition">
                Cek Status PBB
              </a>
            </div>

            {/* Kartu Bansos */}
            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center space-x-3 mb-4">
                <Shield className="w-8 h-8 text-secondary-600" />
                <h3 className="text-xl font-bold text-gray-800">Bantuan Sosial</h3>
              </div>
              <div className="mb-4">
                <p className="text-3xl font-bold text-secondary-600">456</p>
                <p className="text-sm text-gray-600">Penerima Bansos Aktif</p>
              </div>
              <p className="text-sm text-gray-600 mb-4">
                Program aktif: PKH, BLT Dana Desa, BPNT
              </p>
              <a href="/bansos/cek" className="inline-block w-full text-center px-4 py-2 bg-secondary-600 text-white rounded-md hover:bg-secondary-700 transition">
                Cek Status Bansos
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Sambutan Kepala Desa */}
      <section className="py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="bg-white rounded-lg shadow-md overflow-hidden">
            <div className="md:flex">
              <div className="md:w-1/3 bg-gray-100 p-6 flex items-center justify-center">
                <div className="w-48 h-48 bg-primary-200 rounded-full flex items-center justify-center">
                  <span className="text-primary-600 text-lg font-semibold">Foto Kades</span>
                </div>
              </div>
              <div className="md:w-2/3 p-6">
                <h2 className="text-2xl font-bold text-gray-800 mb-2">Sambutan Kepala Desa</h2>
                <p className="text-primary-600 font-medium mb-4">Nama Kepala Desa</p>
                <p className="text-gray-600 leading-relaxed">
                  Assalamu'alaikum Warahmatullahi Wabarakatuh. Selamat datang di website resmi Desa Kasomalang Kulon. 
                  Website ini kami hadirkan sebagai media informasi dan komunikasi antara pemerintah desa dengan masyarakat. 
                  Kami berkomitmen untuk mewujudkan tata kelola pemerintahan desa yang transparan, akuntabel, dan melayani.
                </p>
                <a href="/profil/sejarah" className="inline-block mt-4 text-primary-600 hover:text-primary-700 font-medium">
                  Baca selengkapnya →
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Berita Terkini */}
      <section className="py-12 bg-gray-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-bold text-gray-800 mb-6">Berita Terkini</h2>
          <div className="grid md:grid-cols-3 gap-6">
            {[1, 2, 3].map((item) => (
              <div key={item} className="bg-white rounded-lg shadow-md overflow-hidden">
                <div className="h-48 bg-gray-200 flex items-center justify-center">
                  <span className="text-gray-500">Gambar Berita</span>
                </div>
                <div className="p-4">
                  <span className="text-xs text-primary-600 font-medium">Berita Desa</span>
                  <h3 className="text-lg font-semibold text-gray-800 mt-1 mb-2">
                    Judul Berita Contoh {item}
                  </h3>
                  <p className="text-sm text-gray-600 line-clamp-2">
                    Ringkasan singkat dari berita ini untuk memberikan gambaran isi konten kepada pembaca.
                  </p>
                  <p className="text-xs text-gray-400 mt-3">1 Januari 2024</p>
                </div>
              </div>
            ))}
          </div>
          <div className="text-center mt-6">
            <a href="/berita" className="inline-block px-6 py-2 border-2 border-primary-600 text-primary-600 font-medium rounded-md hover:bg-primary-600 hover:text-white transition">
              Lihat Semua Berita
            </a>
          </div>
        </div>
      </section>

      {/* Peta Lokasi */}
      <section className="py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-bold text-gray-800 mb-6 text-center">Lokasi Kantor Desa</h2>
          <div className="bg-white rounded-lg shadow-md overflow-hidden">
            <div className="h-96 bg-gray-200 flex items-center justify-center">
              <div className="text-center">
                <MapPin className="w-12 h-12 text-gray-400 mx-auto mb-2" />
                <p className="text-gray-500">Embed Google Maps</p>
                <p className="text-sm text-gray-400">Desa Kasomalang Kulon, Kab. Subang</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-800 text-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-3 gap-8">
            <div>
              <h3 className="text-lg font-bold mb-4">Desa Kasomalang Kulon</h3>
              <p className="text-gray-400 text-sm">
                Alamat: Jl. Raya Kasomalang Kulon No. 1<br />
                Kecamatan Kasomalang<br />
                Kabupaten Subang, Jawa Barat
              </p>
            </div>
            <div>
              <h3 className="text-lg font-bold mb-4">Link Cepat</h3>
              <ul className="space-y-2 text-sm text-gray-400">
                <li><a href="/profil/sejarah" className="hover:text-white">Profil Desa</a></li>
                <li><a href="/pbb" className="hover:text-white">Informasi PBB</a></li>
                <li><a href="/bansos/program" className="hover:text-white">Program Bansos</a></li>
                <li><a href="/pengaduan" className="hover:text-white">Pengaduan</a></li>
              </ul>
            </div>
            <div>
              <h3 className="text-lg font-bold mb-4">Kontak</h3>
              <ul className="space-y-2 text-sm text-gray-400">
                <li>Telp: (0260) XXXXXX</li>
                <li>Email: admin@kasomalangkulon.id</li>
                <li>WhatsApp: 08XX-XXXX-XXXX</li>
              </ul>
            </div>
          </div>
          <div className="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
            <p>&copy; 2024 Pemerintah Desa Kasomalang Kulon. All rights reserved.</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
