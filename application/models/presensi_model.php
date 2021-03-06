<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Presensi_model extends CI_Model {

	public function get_all($hari_ini)
	{
		$this->db->select('presensi.id,presensi.tanggal,presensi.karyawan_id,presensi.jam_masuk,presensi.jam_keluar,karyawan.nama_depan,karyawan.nama_belakang,jabatan.nama_jabatan');
		$this->db->join('karyawan','karyawan.nik = presensi.karyawan_id', 'left');
		$this->db->join('jabatan', 'jabatan.id = karyawan.jabatan_id');
		$this->db->where('presensi.tanggal', $hari_ini);
		$query = $this->db->get('presensi');
		if ($query->num_rows() > 0) {
			return $query->result_array();	
		} else {
			return null;
		}
		
	}

	public function check_in($nik, $hari_ini)    
	{
		date_default_timezone_set('Asia/Jakarta');
		$masuk = $this->sudah_masuk($nik, $hari_ini);
		if ($masuk) {
			$this->keluar($nik, $hari_ini);
		} else {
			$this->db->select('nik');
			$query = $this->db->get_where('karyawan', array('nik'=>$nik));
			if ($query->num_rows() > 0) {
				$this->masuk($nik, $hari_ini);
			}
			return false;
		}
	}

	public function sudah_masuk($nik, $hari_ini)
	{
		$this->db->select('*');
		$query = $this->db->get_where('presensi', array('karyawan_id'=>$nik, 'tanggal'=>$hari_ini));
		if (($query->num_rows() > 0) && ($query->num_rows() < 2)) {
			return true;
		} else {
			return false;	
		}
	}

	public function masuk($nik, $hari_ini) {
		$time = date('H:i:s');
		$data = array(
				'jam_masuk' =>$time,
				'karyawan_id' => $nik,
				'tanggal' => $hari_ini
			);
		return $this->db->insert('presensi', $data);
	}

	public function keluar($nik, $hari_ini) {
		$time = date('H:i:s');
		$data = array(
				'jam_keluar' =>$time,
				'tanggal' => $hari_ini
			);
		$this->db->where('karyawan_id', $nik);
		return $this->db->update('presensi', $data);
	}

	public function get_rekap($dari, $sampai)
	{
		$this->db->select('presensi.*, karyawan.nama_depan, karyawan.nama_belakang');
		$this->db->join('presensi', 'presensi.karyawan_id = karyawan.nik', 'left');
		$this->db->where('presensi.tanggal >= ', $dari);
		$this->db->where('presensi.tanggal <=', $sampai);
		$query = $this->db->get('karyawan');
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return null;
		}
	}

}

/* End of file presensi_model.php */
/* Location: ./application/models/presensi_model.php */
