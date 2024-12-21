import React, { useEffect, useRef, useState } from 'react';
import axios from "axios";
import Kanban from './Kanban';

const Print = ({ api_url, typeDownload }) => {
    const params = new URLSearchParams(window.location.search);
    const vendor_code = params.get("vendor_code")
    const vendor_alias = params.get("vendor_alias")
    document.title = `${vendor_alias} (${vendor_code})`;
    const API_URL = api_url;  // Adjust this as per your setup
    const [dataPrint, setdataPrint] = useState([]);
    const targetExport = useRef();

    const closeWindowAfterPrint = () => {
        window.close();
    };
    window.addEventListener("afterprint", closeWindowAfterPrint);

    const updateStatusDownload = async () => {
        try {
            const response = await axios.post(`${API_URL}/updateStatus`, {
                vendor_code: vendor_code,
                typeDownload: typeDownload,
            });

            if (response.status === 200) {
                window.print();
                console.log("Success Updated");
            } else {
                console.error("Failed Updated");
            }
        } catch (error) {
            console.error("Error: ", error.response.data.res);
        }
    };

    useEffect(() => {
        const getData = async () => {
            try {
                const urlGetData = typeDownload === "kanban" ? `${API_URL}/getDataByVendor` : `${API_URL}/getDataDN`
                const response = await axios.post(urlGetData, {
                    vendor_code: vendor_code,
                });
                if (response.data.statusCode === 200) {
                    setdataPrint(response.data.res);
                    if(typeDownload === "kanban"){
                        await updateStatusDownload()
                    }
                } else {
                    console.error("Error in response:", response.data.res);
                }
            } catch (error) {
                console.error("Error fetching data:", error.response.data.res);
            }
        };
    
        getData();
    }, [vendor_code]);

    return (
        <div ref={targetExport}>
            {
                typeDownload === "kanban" 
                && dataPrint.map((e) => {
                    const orderKanban = parseInt(e.order_kbn);
                    const ElementKanban = [];
                    for (let i = 1; i <= orderKanban; i++) {
                        let seq = i;
                        seq = seq.toString().padStart(3, '0');
                        ElementKanban.push(
                            <Kanban 
                                key={`${e.id}-${seq}`}
                                plant_code={e.plant_code} 
                                shop_code={e.shop_code} 
                                part_category={e.part_category} 
                                route={e.route} 
                                del_cycle={e.del_cycle} 
                                del_date={e.del_date} 
                                del_time={e.del_time} 
                                doc_no={e.doc_no}
                                job_no={e.job_no}
                                lane={e.lane}
                                lp={e.lp}
                                order_kbn={orderKanban}
                                seq={seq}
                                order_no={e.order_no}
                                packaging_type={e.packaging_type}
                                part_name={e.part_name}
                                part_no={e.part_no}
                                part_type={e.part_type}
                                po_number={e.po_number}
                                rack_address={e.rack_address}
                                trip={e.trip}
                                qty_kanban={e.qty_kanban}
                                vendor_name={e.vendor_name}
                                vendor_code={e.vendor_code}
                                barCode={e.order_no + e.job_no + seq + "/"}
                            />
                        );
                    }
                    return ElementKanban;
                })
            }
        </div>
    );
};

export default Print;
