<?php


namespace App\Controller;


use App\Http\Response;
use App\Repository\VendorRepository;
use App\Service\RequestService;
use App\Service\VendorService;

class Vendor extends AbstractController
{

    /**
     * @param VendorRepository $vendorRepository
     *
     * @Route(url="/vendor/list")
     *
     * @return Response
     */
    public function list(VendorRepository $vendorRepository) {
        $vendors = $vendorRepository->findAll();

        return $this->render('vendor/index.tpl', [
            'vendors' => $vendors,
        ]);
    }


    /**
     * @param VendorRepository $vendorRepository
     *
     * @Route(url="/vendor/edit")
     * @Route(url="/vendor/edit/{vendor_id}")
     *
     * @return Response
     */
    public function edit(VendorRepository $vendorRepository) {
        $vendor_id = (int) $this->getRoute()->getParam('vendor_id');

        $vendor = $vendorRepository->findOrCreate($vendor_id);

        return $this->render('vendor/edit.tpl', [
            'vendor' => $vendor,
        ]);
    }


    /**
     * @param VendorRepository $vendorRepository
     *
     * @Route(url="/vendor/editing")
     *
     * @return Response
     */
    public function editing(VendorRepository $vendorRepository) {
        $vendor_id = $this->request->getIntFromPost('vendor_id');
        $name = $this->request->getStringFromPost('name');

        if (!$name) {
            die('Name required');
        }

        $vendor = $vendorRepository->findOrCreate($vendor_id);
        $vendor->setName($name);

        $vendorRepository->save($vendor);

        return $this->redirectToList();
    }


    /**
     * @param VendorRepository $vendorRepository
     *
     * @Route(url="/vendor/delete")
     *
     * @return Response
     */
    public function delete(VendorRepository $vendorRepository) {
        $vendor_id = $this->request->getIntFromPost('vendor_id');

        $vendor = $vendorRepository->find($vendor_id);

        $vendorRepository->delete($vendor);

        return $this->redirectToList();
    }

    private function redirectToList() {
        return $this->redirect('/vendor/list');
    }
}