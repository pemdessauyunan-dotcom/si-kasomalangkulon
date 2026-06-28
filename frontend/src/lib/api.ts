import axios from 'axios';
import { API_URL } from './constants';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor untuk menambahkan token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor untuk handle error
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;

// Auth API
export const authApi = {
  login: (data: { email: string; password: string }) => 
    api.post('/auth/login', data),
  logout: () => api.post('/auth/logout'),
  me: () => api.get('/auth/me'),
  changePassword: (data: { current_password: string; new_password: string; new_password_confirmation: string }) =>
    api.put('/auth/change-password', data),
};

// PBB API
export const pbbApi = {
  getList: (params?: Record<string, string>) => api.get('/pbb', { params }),
  getDetail: (id: number) => api.get(`/pbb/${id}`),
  create: (data: FormData) => api.post('/pbb', data, {
    headers: { 'Content-Type': 'multipart/form-data' },
  }),
  approve: (id: number) => api.post(`/pbb/${id}/approve`),
  reject: (id: number, data: { catatan_penolakan: string }) => api.post(`/pbb/${id}/reject`, data),
  getStatistics: (tahun?: number) => api.get('/pbb/statistics', { params: { tahun } }),
  checkPublic: (keyword: string) => api.post('/pbb/check-public', { keyword }),
};

// Bansos API
export const bansosApi = {
  getPrograms: (status?: string) => api.get('/bansos/programs', { params: { status } }),
  getProgramDetail: (id: number) => api.get(`/bansos/programs/${id}`),
  getRecipients: (params?: Record<string, string>) => api.get('/bansos/recipients', { params }),
  propose: (data: { warga_id: number; program_bansos_id: number; alasan_pengajuan: string }) =>
    api.post('/bansos/propose', data),
  approve: (id: number) => api.post(`/bansos/${id}/approve`),
  reject: (id: number, data: { catatan_penolakan: string }) => api.post(`/bansos/${id}/reject`, data),
  checkPublic: (nik: string) => api.post('/bansos/check-public', { nik }),
  getStatistics: () => api.get('/bansos/statistics'),
};

// Berita API
export const beritaApi = {
  getList: (params?: Record<string, string>) => api.get('/berita', { params }),
  getDetail: (slug: string) => api.get(`/berita/${slug}`),
  create: (data: FormData) => api.post('/berita', data, {
    headers: { 'Content-Type': 'multipart/form-data' },
  }),
  update: (id: number, data: FormData) => api.put(`/berita/${id}`, data, {
    headers: { 'Content-Type': 'multipart/form-data' },
  }),
  delete: (id: number) => api.delete(`/berita/${id}`),
};

// Pengaduan API
export const pengaduanApi = {
  getList: (params?: Record<string, string>) => api.get('/pengaduan', { params }),
  create: (data: FormData) => api.post('/pengaduan', data, {
    headers: { 'Content-Type': 'multipart/form-data' },
  }),
  updateStatus: (id: number, data: { status: string; tindak_lanjut?: string }) =>
    api.put(`/pengaduan/${id}/status`, data),
};
