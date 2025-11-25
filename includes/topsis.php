<?php
class TOPSIS {
    private $conn;
    private $bobot;
    
    public function __construct($conn, $bobot) {
        $this->conn = $conn;
        $this->bobot = $bobot;
    }
    
    public function getAlternatif() {
        $stmt = $this->conn->query("SELECT * FROM alternatif");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getKriteria() {
        $stmt = $this->conn->query("SELECT * FROM kriteria ORDER BY id_kriteria");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function hitung() {
        $alternatif = $this->getAlternatif();
        $kriteria = $this->getKriteria();
        
        // Matriks Keputusan
        $matriks = [];
        foreach ($alternatif as $key => $alt) {
            $matriks[$key] = [
                $alt['harga'],
                $alt['jarak'],
                $alt['fasilitas'],
                $alt['keamanan'],
                $alt['kebersihan']
            ];
        }
        
        // Normalisasi Matriks
        $matriksNormalisasi = $this->normalisasi($matriks);
        
        // Matriks Ternormalisasi Terbobot
        $matriksTerbobot = $this->terapkanBobot($matriksNormalisasi);
        
        // Solusi Ideal Positif dan Negatif
        $idealPositif = $this->hitungIdealPositif($matriksTerbobot, $kriteria);
        $idealNegatif = $this->hitungIdealNegatif($matriksTerbobot, $kriteria);
        
        // Jarak dan Preferensi
        $hasil = [];
        foreach ($alternatif as $key => $alt) {
            $jarakPositif = $this->hitungJarak($matriksTerbobot[$key], $idealPositif);
            $jarakNegatif = $this->hitungJarak($matriksTerbobot[$key], $idealNegatif);
            $preferensi = $jarakNegatif / ($jarakPositif + $jarakNegatif);
            
            $hasil[] = [
                'alternatif' => $alt,
                'preferensi' => $preferensi
            ];
        }
        
        // Urutkan berdasarkan preferensi
        usort($hasil, function($a, $b) {
            return $b['preferensi'] <=> $a['preferensi'];
        });
        
        return $hasil;
    }
    
    private function normalisasi($matriks) {
        $jumlahKuadrat = array_fill(0, 5, 0);
        
        foreach ($matriks as $row) {
            foreach ($row as $key => $val) {
                $jumlahKuadrat[$key] += pow($val, 2);
            }
        }
        
        $pembagi = array_map('sqrt', $jumlahKuadrat);
        
        $hasil = [];
        foreach ($matriks as $i => $row) {
            foreach ($row as $j => $val) {
                $hasil[$i][$j] = $val / $pembagi[$j];
            }
        }
        
        return $hasil;
    }
    
    private function terapkanBobot($matriks) {
        $hasil = [];
        foreach ($matriks as $i => $row) {
            foreach ($row as $j => $val) {
                $hasil[$i][$j] = $val * $this->bobot[$j];
            }
        }
        return $hasil;
    }
    
    private function hitungIdealPositif($matriks, $kriteria) {
        $ideal = [];
        for ($j = 0; $j < 5; $j++) {
            $kolom = array_column($matriks, $j);
            $ideal[$j] = ($kriteria[$j]['jenis'] == 'benefit') ? max($kolom) : min($kolom);
        }
        return $ideal;
    }
    
    private function hitungIdealNegatif($matriks, $kriteria) {
        $ideal = [];
        for ($j = 0; $j < 5; $j++) {
            $kolom = array_column($matriks, $j);
            $ideal[$j] = ($kriteria[$j]['jenis'] == 'benefit') ? min($kolom) : max($kolom);
        }
        return $ideal;
    }
    
    private function hitungJarak($vektor1, $vektor2) {
        $jumlah = 0;
        for ($i = 0; $i < count($vektor1); $i++) {
            $jumlah += pow($vektor1[$i] - $vektor2[$i], 2);
        }
        return sqrt($jumlah);
    }
}
?>
