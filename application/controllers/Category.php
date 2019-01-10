<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Category extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('upload');
        $this->load->library('session');
        $this->load->model("category_model");
        $this->load->helper(array('form', 'file', 'url'));
        if (!$this->session->userdata('logged_in')) {
            redirect('login', 'refresh');
        }
    }

    /*  public function index(){
           $this->load->model("category_model");
           $data['list'] = $this->category_model->getlist();
           $this->load->view("category/index",$data);
         // $this->load->view('category/add');
       } */
    public function add()
    {
        $this->load->helper(array('url', 'file', 'form'));
        $this->load->library('form_validation');
        if ($this->input->post("btnadd")) {
            /*  echo "<pre>";
              print_r($_FILES);
              die; */
            $data["name"] = $this->input->post("name");  //get image name
            $info = pathinfo($_FILES['image']['name']);
            $ext = $info['extension']; // get the extension of the file
            $img_path = 'images/categories/' . time() . '.' . $ext;
            $data["image"] = basename($img_path);//get name image
            $this->form_validation->set_rules("name", "Name", "required|min_length[3]");
            if ($this->form_validation->run() == true) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $img_path)) {
                    if ($this->db->insert("category", $data)) {
                        redirect('category/show_category_id');
                    }
                    echo "<pre>";
                    print_r($img_path);
                    die;
                } else {
                    echo "<pre>";
                    print_r("false upload");
                    die;
                }
            } else {
                echo "<pre>";
                print_r("false validation");
                die;
            }


            /* Khi mày dùng thư viện nhưng nó bị lỗi :)
            if(!empty($FILES['image']['name'])){
                   $config['upload_path'] = './images/categories/';
                   $config['allowed_types'] = 'jpg|jpeg|png|gif';
                   $config['file_name'] = $_FILES['image']['name'];
                   $config['max_size']             = 500000;
                   $config['max_width']            = 3000;
                   $config['max_height']           = 3000;

                   $this->load->library('upload',$config);
                   $this->upload->initialize($config);
                   if(!$this->upload->do_upload('image')){
                       $error = array('error' => $this->upload->display_errors());
                       $this->load->view('category/add', $error);

                   }else{
                       $data = array('upload_data' =>$this->upload->data());
                       $this->load->view('category/add',$data);
                      // $uploadData=$this->upload->data();
                     //  $data["image"] = $uploadData['file_name'];
                   }
               }
            /*  echo "<pre>";
               print_r($_FILES);
               die; */
            /* $this->form_validation->set_rules("name","Name",'required|min_length[3]');
           if($this->form_validation->run()==true){
               $this->db->insert("category",$data);
               redirect('category/index');
           } */
        }
        $this->load->view('category/add');
    }

    public function delete_category($id)
    {
        $this->db->where("id", $id);
        $this->db->delete('category');
        redirect("category/show_category_id");
    }

    public function show_category_id()
    {
        $id = $this->uri->segment(3); //lay id hien tai
        $data['category'] = $this->category_model->getList();
        $data['single_category'] = $this->category_model->show_category_id($id);
        $data['content'] = 'modules';
        $this->load->view('category/index', $data);
       // $this->load->view('category/index', $data);
    }
    public function update_category_id()
    {
        $id = $this->input->post('id');
        $image_now = $this->input->post('name_image');
       /* print_r($image_now);
        die; */
        if($this->input->post('dsubmit')){
            $data['name'] = $this->input->post('name');

            if(isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])){
                $info = pathinfo($_FILES['image']['name']);
                $ext = $info['extension']; // get the extension of the file
                $image_save1 = 'images/categories/' . time() . '.' . $ext;
                unlink('images/categories/'.$image_now);
                move_uploaded_file($_FILES['image']['tmp_name'], $image_save1);
               // $image_save = "./image/category/".$_FILES['image']['name'];
                $image_save = basename($image_save1);//get name image
                $data['image'] = $image_save;
            }
            $this->category_model->update_category_id($id,$data);
            redirect('category/show_category_id');
        }

    }
}