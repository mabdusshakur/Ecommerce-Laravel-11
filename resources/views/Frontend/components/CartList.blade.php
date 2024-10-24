<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-title">
                    <h1>Cart List</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">This Page</a></li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>

<div class="mt-5">
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive shop_cart_table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="product-thumbnail">&nbsp;</th>
                                <th class="product-name">Product</th>
                                <th class="product-quantity">Quantity</th>
                                <th class="product-subtotal">Total</th>
                                <th class="product-remove">Remove</th>
                            </tr>
                        </thead>
                        <tbody id="byList">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="px-0" colspan="6">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-lg-4 col-md-6 mb-md-0 mb-3">
                                            Total: $ <span id="total"></span>
                                        </div>
                                        <div class="col-lg-8 col-md-6 text-md-end text-start">
                                            <button class="btn btn-line-fill btn-sm" type="submit" onclick="CheckOut()">Check Out</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function CartList() {
        let res = await axios.get("api/CartList");
        let cart = res.data.data;

        $("#byList").empty();

        cart.forEach((item, i) => {
            let EachItem = `<tr>
                            <td class="product-thumbnail"><img src=${item['product']['image']} alt="product"></td>
                            <td class="product-name" >${item['product']['title']} </td>
                            <td class="product-quantity"> ${item['quantity']} </td>
                            <td class="product-subtotal">$ ${item['price']}</td>
                            <td class="product-remove"><a class="remove" data-id="${item['product_id']}"><i class="ti-close"></i></a></td>
                        </tr>`
            $("#byList").append(EachItem);
        })
        await CartTotal(cart);

        $('.remove').on('click', function() {
            let id = $(this).data('id');
             RemoveCartList(id);
        })
    }
    async function CartTotal(cart) {
        let Total = 0;
        cart.forEach((item, i) => {
            Total = Total + parseFloat(item['price']);
        })
        $('#total').text(Total);
    }
    async function RemoveCartList(id) {
        $(".preloader").delay(90).fadeIn(100).removeClass('loaded');
        let res = await axios.get("api/DeleteCartList/" + id);
        $(".preloader").delay(90).fadeOut(100).addClass('loaded');
        if (res.status === 200) {
            await CartList();
        } else {
            errorToast("Request Fail")
        }
    }

    async function CheckOut(){
        $(".preloader").delay(90).fadeIn(100).removeClass('loaded');

        $("#paymentList").empty();

        let res=await axios.get("api/InvoiceCreate");

        $(".preloader").delay(90).fadeOut(100).addClass('loaded');

        let paymentmethod=res.data.data.paymentMethod;

        if(res.status===200) {

            $("#paymentMethodModal").modal('show');

            paymentmethod.desc.forEach((item,i)=>{
                let EachItem=`<tr>
                                <td><img class="w-50" src=${item['logo']} alt="product"></td>
                                <td><p>${item['name']}</p></td>
                                <td><a class="btn btn-danger btn-sm" href="${item['redirectGatewayURL']}">Pay</a></td>
                            </tr>`
                $("#paymentList").append(EachItem);
            })

        }
        else{
            alert("Request Fail");
        }
}



</script>
