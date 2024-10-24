<div class="modal animated zoomIn" id="update-modal" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Update Brand</h6>
            </div>
            <div class="modal-body">
                <form id="update-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">
                                <label class="form-label">Brand Name *</label>
                                <input class="form-control" id="brandNameUpdate" type="text">

                                <br />
                                <img class="w-15" id="oldImg" src="{{ asset('admin/images/default.jpg') }}" />
                                <br />

                                <label class="form-label">Image</label>
                                <input class="form-control" id="brandImgUpdate" type="file" oninput="oldImg.src=window.URL.createObjectURL(this.files[0])">
                                <input class="d-none" id="updateID" type="text">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn bg-gradient-primary" id="modal-close" data-bs-dismiss="modal" aria-label="Close">Close</button>
                <button class="btn bg-gradient-success" id="save-btn" onclick="update()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    async function FillUpUpdateForm(id) {
        document.getElementById('updateID').value = id;
        showLoader();
        let res = await axios.get(`/api/brands/${id}`);
        
        hideLoader();
        document.getElementById('brandNameUpdate').value = res.data.data.name;
        document.getElementById('oldImg').src = ' {{ config('app.url') }}/' +res.data.data.image;
    }

    async function update() {
        let brandName = document.getElementById('brandNameUpdate').value;
        let brandImg = document.getElementById('brandImgUpdate').files[0];
        let id = document.getElementById('updateID').value;

        if (brandName.length === 0) {
            errorToast("brand Required !")
        } else {

            let formData = new FormData();
            formData.append('name', brandName)
            if (brandImg) {
                formData.append('image', brandImg)
            }
            formData.append('_method', 'put')

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }

            showLoader();
            let res = await axios.post(`/api/brands/${id}`, formData, config)
           
            hideLoader();

            if (res.status === 200 ) {
                successToast(res.data['msg']);
                $("#update-modal").modal('hide');
                document.getElementById("update-form").reset();
                await getList();
            } else {
                errorToast(res.data['msg'])
            }
        }
    }
</script>
