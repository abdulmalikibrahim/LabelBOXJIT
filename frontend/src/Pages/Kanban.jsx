import React from 'react';
import QRCode from "react-qr-code";
import Barcode from "react-barcode";

const Kanban = ({...props}) => {
    return(
        <div className='row kanban pb-4'>
            <div className="col-3 pe-2" key={"SideLeft"}>
                <table className='w-100' style={{height:"100%"}}>
                    <tr>
                        <td className='border border-dark text-center' style={{height:"30px"}}>ARRIVAL DATE</td>
                        <td className='border border-dark text-center'>CYCLE</td>
                    </tr>
                    <tr>
                        <td className='fw-bold border border-dark text-center'>{props.del_date+" "+props.del_time}</td>
                        <td className='border border-dark text-center' rowSpan={2}><h1>1<label style={{fontSize:20}}>/{props.del_cycle}</label></h1></td>
                    </tr>
                    <tr>
                        <td className='border border-dark fw-bold text-center' style={{height:"20%", fontSize:27}}>{props.route}</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className='fw-bold border border-dark text-center p-3' style={{fontSize:26}}>
                            {props.vendor_name}
                            <br/>{props.vendor_code} 
                        </td>
                    </tr>
                </table>
            </div>
            <div className="col-6 p-0" key={"Content"}>
                <table className='w-100' style={{height:"100%"}}>
                    <tr>
                        <td className='border border-dark text-center' style={{height:"30px"}}>SHOP</td>
                        <td className='border border-dark text-center'>DN NO</td>
                    </tr>
                    <tr>
                        <td className='fw-bold border border-dark text-center' rowSpan={2} style={{fontSize:"25pt"}}>{props.shop_code}</td>
                        <td className='fw-bold border border-dark text-center text-center pt-2 pb-2' style={{fontSize:30}}>{props.order_no}</td>
                    </tr>
                    <tr>
                        <td className='border border-dark text-center'>PART INFORMATION</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className='fw-bold border border-dark text-center'>
                            <table className="w-100" style={{height:"100%"}}>
                                <tr>
                                    <td rowSpan={3} style={{width:170}} className='p-3'>{CreateQRCode({value:props.barCode})}</td>
                                </tr>
                                <tr>
                                    <td className='border-dark border-start border-end text-center'>JOB NO</td>
                                    <td>QTY/KBN</td>
                                </tr>
                                <tr>
                                    <td className='fw bold border-dark border-start border-end border-top text-center' style={{fontSize:45}}>{props.job_no}</td>
                                    <td className='text-center border-dark border-top' style={{fontSize:50}}>{props.qty_kanban}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colSpan={3} className='text-center fw-bold border border-dark' style={{fontSize:25}}>{props.part_no}</td>
                    </tr>
                    <tr>
                        <td colSpan={3} className='text-center border border-dark' style={{fontSize:25}}>{props.part_name}</td>
                    </tr>
                    <tr>
                        <td colSpan={3} className='text-center border border-dark pt-2 pb-2'>
                            <table className='w-100'>
                                <tr>
                                    <td colSpan={2}>{CreateBarcode({value:props.barCode})}</td>
                                </tr>
                                <tr className='fw-bold' style={{fontSize:20}}>
                                    <td>{props.order_no+props.job_no}</td>
                                    <td>SEQ : {props.seq}/{props.order_kbn}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div className="col-3 ps-2" key={"SideRight"}>
                <table className="w-100" style={{height:"100%"}}>
                    <tr>
                        <td colSpan={4} className="border border-dark text-center fw-bold">PT ADM - ASSY PLANT</td>
                    </tr>
                    <tr>
                        <td className="border border-dark text-center fw-bold" style={{width:"25%"}}>MIN</td>
                        <td className="border border-dark text-center fw-bold" style={{width:"25%"}}>0</td>
                        <td className="border border-dark text-center fw-bold" style={{width:"25%"}}>MAX</td>
                        <td className="border border-dark text-center fw-bold" style={{width:"25%"}}>0</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className="border border-dark text-center">PART CAT</td>
                        <td colSpan={2} className="border border-dark text-center">PACKAGING TYPE</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className="border border-dark text-center fw-bold">{props.part_category}</td>
                        <td colSpan={2} className="border border-dark text-center fw-bold">{props.packaging_type}</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className="border border-dark text-center">AREA CODE</td>
                        <td colSpan={2} className="border border-dark text-center">LANE NO</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className="border border-dark text-center fw-bold"></td>
                        <td colSpan={2} className="border border-dark text-center fw-bold" style={{fontSize:"50pt"}}>{props.lane}</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className="border border-dark text-center">PART TYPE</td>
                        <td colSpan={2} className="border border-dark text-center">WH ZONE</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className="border border-dark text-center" style={{height:"30px"}}>{props.part_type}</td>
                        <td colSpan={2} className="border border-dark text-center">{props.wh_zone}</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className="border border-dark text-center">RACK NO</td>
                        <td colSpan={2} className="border border-dark text-center">RACK LAYER</td>
                    </tr>
                    <tr>
                        <td colSpan={2} className="border border-dark text-center" style={{height:"30px"}}>{props.rack_address}</td>
                        <td colSpan={2} className="border border-dark text-center"></td>
                    </tr>
                </table>
            </div>
        </div>
    )
}

const CreateQRCode = ({value}) => {
    return(
        <QRCode
            size={500}
            style={{ height: "auto", maxWidth: "100%", width: "100%" }}
            value={value}
            viewBox={`0 0 500 500`}
        />
    )
}

const CreateBarcode = ({value}) => {
    return(
        <Barcode value={value} displayValue={false} height={70} width={2.4} />
    )
}

export default Kanban;