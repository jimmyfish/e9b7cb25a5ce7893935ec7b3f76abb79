<?php

namespace OfficeBundle\Controller;

use OfficeBundle\Entity\Attachment;
use OfficeBundle\Entity\Holiday;
use OfficeBundle\Entity\UserFamily;
use OfficeBundle\Entity\UserJob;
use OfficeBundle\Entity\UserPersonal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    public function listUserFromUsernameAction($username)
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(UserPersonal::class)->findBy(['username' => $username]);

        return new JsonResponse($data);
    }

    public function listHolidayAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $data = $manager->getRepository(Holiday::class)->findAll();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['id'] = $item->getId();
            $results[$i]['title'] = $item->getTitle();
            $results[$i]['start'] = date('c', strtotime($item->getDay()->format('d-m-Y')));
            $results[$i]['allDay'] = 'true';
            $results[$i]['end'] = $results[$i]['start'];

            ++$i;
        }

        return new JsonResponse($results);
    }

    public function getDataAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

        $job = $em->getRepository(UserJob::class)->findByUserId($user);

        $family = $em->getRepository(UserFamily::class)->findByUserId($user);

        $results = [];

        if (0 == $user->getStatus()) {
            $results['nama'] = $user->getNama();
            $results['nama_registrasi'] = $user->getNoRegistrasi();
            $results['tempat-lahir'] = $user->getTempatLahir();
            $results['tanggal-lahir'] = $user->getTanggalLahir();
            $results['tanggal-pensiun'] = $user->getTanggalPensiun();
            $results['jenis-kelamin'] = $user->getJenisKelamin();
            $results['alamat-surat'] = $user->getAlamatSurat();
            $results['golongan-darah'] = $user->getGolonganDarah();
            $results['no-ktp'] = $user->getNoKtp();
            $results['agama'] = $user->getAgama();
            $results['kebangsaan'] = $user->getKebangsaan();
            $results['pendidikan'] = $user->getPendidikan();
            $results['asal-sekolah'] = $user->getAsalSekolah();
            $results['jurusan'] = $user->getJurusan();
            $results['bpjs'] = $user->getBpjs();
            $results['npwp'] = $user->getNpwp();
            $results['no-telp'] = $user->getNoTelp();
            $results['ukuran-pakaian'] = $user->getUkuranPakaian();
        } else {
            $results['golongan'] = $job->getGolongan();
            $results['jenjang-pangkat'] = $job->getJenjangPangkat();
            $results['tanggal-masuk'] = $job->getTanggalMasuk();
            $results['status-karyawan'] = $job->getStatusKaryawan();
            $results['pengalaman-kerja-terakhir'] = $job->getPengalamanKerjaTerakhir();
            $results['kontrak-training'] = $job->getKontrakTraining();
            $results['kontrak-kerja'] = $job->getKontrakKerja();
            $results['alamat-orang-tua'] = $family->getAlamatOrangTua();
        }

        return new JsonResponse($results);
    }

    public function testingAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Attachment::class, 'u')->where('u.isValidated = 0');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['username'] = $item->getUserId()->getUsername();
            $results[$i]['description'] = $item->getDescription();

            ++$i;
        }

        return new JsonResponse($results);
//        return var_dump($data);
    }

    public function notifUserAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->getRepository(Attachment::class)->findBy(['userId' => $user]);

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['username'] = $item->getUserId()->getUsername();
            $results[$i]['description'] = $item->getDescription();

            ++$i;
        }

        return new JsonResponse($results);
    }

    public function notifValidatorAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->createQueryBuilder()
            ->select('attachment')
            ->from('OfficeBundle:UserPersonal', 'user')
            ->innerJoin('OfficeBundle:Attachment', 'attachment')
            ->where('user.id = attachment.userId');

        $i = 0;

        foreach ($data->getQuery()->getResult() as $item) {
            if ($item->getUserId()->getPenempatan() == $user->getPenempatan()) {
                $results[$i]['username'] = $item->getUserId()->getNama();
                $results[$i]['description'] = $item->getDescription();
                ++$i;
            }
        }

        return new JsonResponse($results);

//        return var_dump($user->getPenempatan()->getId());
    }
}
