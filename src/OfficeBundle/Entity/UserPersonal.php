<?php

namespace OfficeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserPersonal.
 */
class UserPersonal implements UserInterface, \Serializable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $noRegistrasi;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $nik;

    /**
     * @var string
     */
    private $nama;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $tempatLahir;

    /**
     * @var \DateTime
     */
    private $tanggalLahir;

    /**
     * @var \DateTime
     */
    private $tanggalPensiun;

    /**
     * @var int
     */
    private $jenisKelamin;

    /**
     * @var string
     */
    private $tempatTinggal;

    /**
     * @var string
     */
    private $alamatSurat;

    /**
     * @var string
     */
    private $golonganDarah;

    /**
     * @var int
     */
    private $penempatan;

    /**
     * @var string
     */
    private $noKtp;

    /**
     * @var string
     */
    private $agama;

    /**
     * @var string
     */
    private $kebangsaan;

    /**
     * @var string
     */
    private $pendidikan;

    /**
     * @var string
     */
    private $asalSekolah;

    /**
     * @var string
     */
    private $jurusan;

    /**
     * @var string
     */
    private $bpjs;

    /**
     * @var string
     */
    private $npwp;

    /**
     * @var string
     */
    private $noTelp;

    /**
     * @var string
     */
    private $ukuranPakaian;

    /**
     * @var string
     */
    private $profilePicture;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $role;

    /**
     * @var string
     */
    private $roles;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $isValidated;

    /**
     * @var int
     */
    private $isDeleted;

    private $finger;

    private $url;

    public static function create($username, $nama, $password)
    {
        $user = new self();
        $user->setUsername($username);
        $user->setNama($nama);
        $user->setPassword($password);
        $user->setStatus(0);
        $user->setRole(1);
        $user->setIsValidated(0);
        $user->setIsDeleted(0);

        return $user;
    }

    public function createUser(Request $request)
    {
        $username = $request->get('username');
        $nama = $request->get('nama');
        $password = $request->get('password');

        $createUser = self::create($username, $nama, $password);

        return $createUser;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nik.
     *
     * @param string $nik
     *
     * @return UserPersonal
     */
    public function setNik($nik)
    {
        $this->nik = $nik;

        return $this;
    }

    /**
     * Get nik.
     *
     * @return string
     */
    public function getNik()
    {
        return $this->nik;
    }

    /**
     * Set noregistrasi.
     *
     * @param $noRegistrasi
     *
     * @return UserPersonal
     */
    public function setNoRegistrasi($noRegistrasi)
    {
        $this->noRegistrasi = $noRegistrasi;

        return $this;
    }

    /**
     * Get noregistrasi.
     *
     * @return string
     */
    public function getNoRegistrasi()
    {
        return $this->noRegistrasi;
    }

    /**
     * Set Username.
     *
     * @param $username
     *
     * @return UserPersonal
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set nama.
     *
     * @param string $nama
     *
     * @return UserPersonal
     */
    public function setNama($nama)
    {
        $this->nama = $nama;

        return $this;
    }

    /**
     * Get nama.
     *
     * @return string
     */
    public function getNama()
    {
        return $this->nama;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return UserPersonal
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return UserPersonal
     */
    public function setPassword($password)
    {
        $this->password = md5($password);

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set tempatLahir.
     *
     * @param string $tempatLahir
     *
     * @return UserPersonal
     */
    public function setTempatLahir($tempatLahir)
    {
        $this->tempatLahir = $tempatLahir;

        return $this;
    }

    /**
     * Get tempatLahir.
     *
     * @return string
     */
    public function getTempatLahir()
    {
        return $this->tempatLahir;
    }

    /**
     * Set tanggalLahir.
     *
     * @param \DateTime $tanggalLahir
     *
     * @return UserPersonal
     */
    public function setTanggalLahir($tanggalLahir)
    {
        $this->tanggalLahir = new \DateTime($tanggalLahir);

        return $this;
    }

    /**
     * Get tanggalLahir.
     *
     * @return \DateTime
     */
    public function getTanggalLahir()
    {
        return $this->tanggalLahir;
    }

    /**
     * Set tanggalPensiun.
     *
     * @param \DateTime $tanggalPensiun
     *
     * @return UserPersonal
     */
    public function setTanggalPensiun($tanggalPensiun)
    {
        $this->tanggalPensiun = new \DateTime($tanggalPensiun);

        return $this;
    }

    /**
     * Get tanggalPensiun.
     *
     * @return \DateTime
     */
    public function getTanggalPensiun()
    {
        return $this->tanggalPensiun;
    }

    /**
     * Set jenisKelamin.
     *
     * @param int $jenisKelamin
     *
     * @return UserPersonal
     */
    public function setJenisKelamin($jenisKelamin)
    {
        $this->jenisKelamin = $jenisKelamin;

        return $this;
    }

    /**
     * Get jenisKelamin.
     *
     * @return int
     */
    public function getJenisKelamin()
    {
        return $this->jenisKelamin;
    }

    /**
     * Set tempatTinggal.
     *
     * @param string $tempatTinggal
     *
     * @return UserPersonal
     */
    public function setTempatTinggal($tempatTinggal)
    {
        $this->tempatTinggal = $tempatTinggal;

        return $this;
    }

    /**
     * Get tempatTinggal.
     *
     * @return string
     */
    public function getTempatTinggal()
    {
        return $this->tempatTinggal;
    }

    /**
     * Set alamatSurat.
     *
     * @param string $alamatSurat
     *
     * @return UserPersonal
     */
    public function setAlamatSurat($alamatSurat)
    {
        $this->alamatSurat = $alamatSurat;

        return $this;
    }

    /**
     * Get alamatSurat.
     *
     * @return string
     */
    public function getAlamatSurat()
    {
        return $this->alamatSurat;
    }

    /**
     * Set golonganDarah.
     *
     * @param string $golonganDarah
     *
     * @return UserPersonal
     */
    public function setGolonganDarah($golonganDarah)
    {
        $this->golonganDarah = $golonganDarah;

        return $this;
    }

    /**
     * Get golonganDarah.
     *
     * @return string
     */
    public function getGolonganDarah()
    {
        return $this->golonganDarah;
    }

    /**
     * Set penempatan.
     *
     * @param int $penempatan
     *
     * @return self
     */
    public function setPenempatan(CompanyProfile $penempatan)
    {
        $this->penempatan = $penempatan;

        return $this;
    }

    /**
     * Get penempatan.
     *
     * @return int
     */
    public function getPenempatan()
    {
        return $this->penempatan;
    }

    /**
     * Set noKtp.
     *
     * @param int $noKtp
     *
     * @return UserPersonal
     */
    public function setNoKtp($noKtp)
    {
        $this->noKtp = $noKtp;

        return $this;
    }

    /**
     * Get noKtp.
     *
     * @return int
     */
    public function getNoKtp()
    {
        return $this->noKtp;
    }

    /**
     * Set agama.
     *
     * @param string $agama
     *
     * @return UserPersonal
     */
    public function setAgama($agama)
    {
        $this->agama = $agama;

        return $this;
    }

    /**
     * Get agama.
     *
     * @return string
     */
    public function getAgama()
    {
        return $this->agama;
    }

    /**
     * Set kebangsaan.
     *
     * @param string $kebangsaan
     *
     * @return UserPersonal
     */
    public function setKebangsaan($kebangsaan)
    {
        $this->kebangsaan = $kebangsaan;

        return $this;
    }

    /**
     * Get kebangsaan.
     *
     * @return string
     */
    public function getKebangsaan()
    {
        return $this->kebangsaan;
    }

    /**
     * Set pendidikan.
     *
     * @param string $pendidikan
     *
     * @return UserPersonal
     */
    public function setPendidikan($pendidikan)
    {
        $this->pendidikan = $pendidikan;

        return $this;
    }

    /**
     * Get pendidikan.
     *
     * @return string
     */
    public function getPendidikan()
    {
        return $this->pendidikan;
    }

    /**
     * Set asalSekolah.
     *
     * @param string $asalSekolah
     *
     * @return UserPersonal
     */
    public function setAsalSekolah($asalSekolah)
    {
        $this->asalSekolah = $asalSekolah;

        return $this;
    }

    /**
     * Get asalSekolah.
     *
     * @return string
     */
    public function getAsalSekolah()
    {
        return $this->asalSekolah;
    }

    /**
     * Set jurusan.
     *
     * @param string $jurusan
     *
     * @return UserPersonal
     */
    public function setJurusan($jurusan)
    {
        $this->jurusan = $jurusan;

        return $this;
    }

    /**
     * Get jurusan.
     *
     * @return string
     */
    public function getJurusan()
    {
        return $this->jurusan;
    }

    /**
     * Set bpjs.
     *
     * @param string $bpjs
     *
     * @return UserPersonal
     */
    public function setBpjs($bpjs)
    {
        $this->bpjs = $bpjs;

        return $this;
    }

    /**
     * Get bpjs.
     *
     * @return string
     */
    public function getBpjs()
    {
        return $this->bpjs;
    }

    /**
     * Set npwp.
     *
     * @param string $npwp
     *
     * @return UserPersonal
     */
    public function setNpwp($npwp)
    {
        $this->npwp = $npwp;

        return $this;
    }

    /**
     * Get npwp.
     *
     * @return string
     */
    public function getNpwp()
    {
        return $this->npwp;
    }

    /**
     * Set noTelp.
     *
     * @param string $noTelp
     *
     * @return UserPersonal
     */
    public function setNoTelp($noTelp)
    {
        $this->noTelp = $noTelp;

        return $this;
    }

    /**
     * Get noTelp.
     *
     * @return string
     */
    public function getNoTelp()
    {
        return $this->noTelp;
    }

    /**
     * Set ukuranPakaian.
     *
     * @param string $ukuranPakaian
     *
     * @return UserPersonal
     */
    public function setUkuranPakaian($ukuranPakaian)
    {
        $this->ukuranPakaian = $ukuranPakaian;

        return $this;
    }

    /**
     * Get ukuranPakaian.
     *
     * @return string
     */
    public function getUkuranPakaian()
    {
        return $this->ukuranPakaian;
    }

    /**
     * Set role.
     *
     * @param int $role
     *
     * @return UserPersonal
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set profilepicture.
     *
     * @param $profilePicture
     *
     * @return UserPersonal
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * Get profilepicture.
     *
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * Set status.
     *
     * @param $status
     *
     * @return UserPersonal
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $token
     *
     * @return UserPersonal
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set isValidated.
     *
     * @return UserPersonal
     */
    public function setIsValidated($isValidated)
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * Get isValidated.
     *
     * @return int
     */
    public function getIsValidated()
    {
        return $this->isValidated;
    }

    /**
     * Set isDeleted.
     *
     * @param int $isDeleted
     *
     * @return UserPersonal
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return int
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Get the value of Finger.
     *
     * @return mixed
     */
    public function getFinger()
    {
        return $this->finger;
    }

    /**
     * Gets the value of roles.
     *
     * @return string
     */
    public function getRoles()
    {
        $roles = unserialize($this->roles);

        return $roles;
    }

    /**
     * Sets the value of roles.
     *
     * @param string $roles the roles
     *
     * @return self
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        return;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    public function eraseCredentials()
    {
    }

    private $isActive;

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public $job;

    public function getJob()
    {
        return $this->job;
    }

    private $cuti;

    /**
     * @return mixed
     */
    public function getCuti()
    {
        return $this->cuti;
    }

    public function countCuti()
    {
        return count($this->cuti);
    }

    public function getCutiOfMonth($month, $year)
    {
        $data = [];
        foreach ($this->cuti as $item) {
            if ($item->getBulan() == $month && $item->getTahun() == $year) {
                array_push($data, $item);
            }
        }

        return $data;
    }

    public function __construct()
    {
        $cuties = new ArrayCollection();
    }

    private $cutiValidate;

    /**
     * @return mixed
     */
    public function getCutiValidate()
    {
        return $this->cutiValidate;
    }

    private $presence;

    /**
     * @return mixed
     */
    public function getPresence()
    {
        return $this->presence;
    }

    // public function getPresenceByMonth($month, $year)
    // {
    //     $data = [];
    //     foreach ($this->presence as $item) {
    //         // if ($item->getBulan() == $month && $item->getTahun() == $year) {
    //         //     array_push($data, $item);
    //         // }
    //         if ($item->getMonth() == $month && $item->getYear() == $year && $item->getState() == )
    //     }

    //     return $data;
    // }

    private $presenceRaw;

    /**
     * @return mixed
     */
    public function getPresenceRaw()
    {
        return $this->presenceRaw;
    }

    /**
     * @param mixed $presenceRaw
     *
     * @return self
     */
    public function setPresenceRaw($presenceRaw)
    {
        $this->presenceRaw = $presenceRaw;

        return $this;
    }
}
