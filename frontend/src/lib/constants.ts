export const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

export const ROLE = {
  ADMIN: 'admin',
  RT: 'rt',
  KOLEKTOR: 'kolektor',
  MASYARAKAT: 'masyarakat',
};

export const STATUS_PBB = {
  PENDING: 'pending',
  APPROVED: 'approved',
  REJECTED: 'rejected',
};

export const STATUS_BANSOS = {
  DIUSULKAN: 'diusulkan',
  DISETUJUI: 'disetujui',
  DITOLAK: 'ditolak',
  NONAKTIF: 'nonaktif',
};

export function getStatusBadgeInfo(status: string, type: 'pbb' | 'bansos') {
  if (type === 'pbb') {
    switch (status) {
      case STATUS_PBB.PENDING:
        return { color: 'yellow', label: 'Pending' };
      case STATUS_PBB.APPROVED:
        return { color: 'green', label: 'Lunas' };
      case STATUS_PBB.REJECTED:
        return { color: 'red', label: 'Ditolak' };
      default:
        return { color: 'gray', label: 'Unknown' };
    }
  } else {
    switch (status) {
      case STATUS_BANSOS.DIUSULKAN:
        return { color: 'yellow', label: 'Diusulkan' };
      case STATUS_BANSOS.DISETUJUI:
        return { color: 'green', label: 'Disetujui' };
      case STATUS_BANSOS.DITOLAK:
        return { color: 'red', label: 'Ditolak' };
      case STATUS_BANSOS.NONAKTIF:
        return { color: 'gray', label: 'Nonaktif' };
      default:
        return { color: 'gray', label: 'Unknown' };
    }
  }
}
