'use client';

import Link from 'next/link';
import { useState } from 'react';
import { Menu, X, ChevronDown, Search } from 'lucide-react';

export default function Navbar() {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [activeDropdown, setActiveDropdown] = useState<string | null>(null);

  const navItems = [
    { label: 'Beranda', href: '/' },
    {
      label: 'Profil Desa',
      href: '#',
      dropdown: [
        { label: 'Sejarah & Visi Misi', href: '/profil/sejarah' },
        { label: 'Struktur Organisasi', href: '/profil/struktur' },
        { label: 'Perangkat Desa', href: '/profil/perangkat' },
        { label: 'Wilayah Administratif', href: '/profil/wilayah' },
        { label: 'Peta Desa', href: '/profil/peta' },
      ],
    },
    {
      label: 'Data & Statistik',
      href: '#',
      dropdown: [
        { label: 'Jumlah Penduduk', href: '/statistik/penduduk' },
        { label: 'Berdasarkan Usia', href: '/statistik/usia' },
        { label: 'Berdasarkan Pendidikan', href: '/statistik/pendidikan' },
        { label: 'Berdasarkan Pekerjaan', href: '/statistik/pekerjaan' },
        { label: 'Berdasarkan Perkawinan', href: '/statistik/perkawinan' },
        { label: 'Berdasarkan Agama', href: '/statistik/agama' },
      ],
    },
    { label: 'PBB', href: '/pbb' },
    {
      label: 'Bansos',
      href: '#',
      dropdown: [
        { label: 'Program Bansos', href: '/bansos/program' },
        { label: 'Cek Status Penerima', href: '/bansos/cek' },
        { label: 'Statistik Penerima', href: '/bansos/statistik' },
        { label: 'Pengumuman', href: '/bansos/pengumuman' },
      ],
    },
    {
      label: 'Layanan',
      href: '#',
      dropdown: [
        { label: 'Permohonan Surat Online', href: '/layanan/surat' },
        { label: 'Pengaduan Masyarakat', href: '/pengaduan' },
        { label: 'Unduh Formulir', href: '/layanan/formulir' },
      ],
    },
    { label: 'Berita', href: '/berita' },
    { label: 'Galeri', href: '/galeri' },
  ];

  return (
    <nav className="bg-white shadow-md sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          {/* Logo */}
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center">
              <span className="text-white font-bold text-lg">KK</span>
            </div>
            <div>
              <h1 className="text-lg font-bold text-gray-800">Desa Kasomalang Kulon</h1>
              <p className="text-xs text-gray-500">Kabupaten Subang</p>
            </div>
          </div>

          {/* Desktop Navigation */}
          <div className="hidden lg:flex items-center space-x-1">
            {navItems.map((item) => (
              <div
                key={item.label}
                className="relative"
                onMouseEnter={() => item.dropdown && setActiveDropdown(item.label)}
                onMouseLeave={() => item.dropdown && setActiveDropdown(null)}
              >
                <Link
                  href={item.href}
                  className="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md flex items-center space-x-1"
                >
                  <span>{item.label}</span>
                  {item.dropdown && <ChevronDown className="w-4 h-4" />}
                </Link>

                {/* Dropdown */}
                {item.dropdown && activeDropdown === item.label && (
                  <div className="absolute left-0 mt-1 w-56 bg-white rounded-md shadow-lg border border-gray-100 py-1 z-50">
                    {item.dropdown.map((subItem) => (
                      <Link
                        key={subItem.label}
                        href={subItem.href}
                        className="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600"
                      >
                        {subItem.label}
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </div>

          {/* Search & Login */}
          <div className="hidden lg:flex items-center space-x-3">
            <button className="p-2 text-gray-500 hover:text-primary-600">
              <Search className="w-5 h-5" />
            </button>
            <div className="relative group">
              <button className="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 flex items-center space-x-1">
                <span>Login</span>
                <ChevronDown className="w-4 h-4" />
              </button>
              <div className="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-100 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                <Link href="/login/admin" className="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">
                  Login Admin Desa
                </Link>
                <Link href="/login/rt" className="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">
                  Login RT
                </Link>
                <Link href="/login/kolektor" className="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">
                  Login Kolektor
                </Link>
                <div className="border-t border-gray-100 my-1"></div>
                <Link href="/login/masyarakat" className="block px-4 py-2 text-sm text-gray-400 cursor-not-allowed">
                  Login Masyarakat (Segera Hadir)
                </Link>
              </div>
            </div>
          </div>

          {/* Mobile menu button */}
          <button
            className="lg:hidden p-2 text-gray-500"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          >
            {isMobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>
      </div>

      {/* Mobile Navigation */}
      {isMobileMenuOpen && (
        <div className="lg:hidden bg-white border-t border-gray-100">
          <div className="px-4 py-2 space-y-1 max-h-[80vh] overflow-y-auto">
            {navItems.map((item) => (
              <div key={item.label}>
                <Link
                  href={item.href}
                  className="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-md"
                >
                  {item.label}
                </Link>
                {item.dropdown && (
                  <div className="pl-6 space-y-1">
                    {item.dropdown.map((subItem) => (
                      <Link
                        key={subItem.label}
                        href={subItem.href}
                        className="block px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded-md"
                      >
                        {subItem.label}
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            ))}
            <div className="border-t border-gray-100 pt-2 mt-2">
              <Link href="/login/admin" className="block px-3 py-2 text-sm text-gray-700 hover:bg-primary-50">
                Login Admin
              </Link>
              <Link href="/login/rt" className="block px-3 py-2 text-sm text-gray-700 hover:bg-primary-50">
                Login RT
              </Link>
              <Link href="/login/kolektor" className="block px-3 py-2 text-sm text-gray-700 hover:bg-primary-50">
                Login Kolektor
              </Link>
            </div>
          </div>
        </div>
      )}
    </nav>
  );
}
