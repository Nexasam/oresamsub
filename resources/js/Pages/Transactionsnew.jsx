import { useState } from "react";
import { usePage, Link } from "@inertiajs/react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import WalletBalance from "@/Components/WalletBalance";

export default function Transactionsnew({ transactions }) {
    const [items, setItems] = useState(transactions.data);
    const [nextPage, setNextPage] = useState(transactions.next_page_url);
    const [loading, setLoading] = useState(false);
    const [search, setSearch] = useState("");

    const [showModal, setShowModal] = useState(false);
    const [selectedTx, setSelectedTx] = useState(null);
    const [phone, setPhone] = useState("");
    const [pin, setPin] = useState("");

    // 🔹 Infinite Scroll
    useEffect(() => {
        const handleScroll = async () => {
            if (
                window.innerHeight + window.scrollY >= document.body.offsetHeight - 200 &&
                nextPage &&
                !loading
            ) {
                setLoading(true);

                const res = await axios.get(nextPage);
                setItems(prev => [...prev, ...res.data.data]);
                setNextPage(res.data.next_page_url);
                setLoading(false);
            }
        };

        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    }, [nextPage, loading]);

    // 🔹 Filter (product plan search)
    const filteredItems = items.filter(tx =>
        tx.product_plan?.toLowerCase().includes(search.toLowerCase())
    );

    // 🔹 Open Modal
    const handleBuyAgain = (tx) => {
        setSelectedTx(tx);
        setShowModal(true);
    };

    // 🔹 Submit Purchase
    const handlePurchase = async () => {
        try {
            await axios.post(route("transactions.buyAgain"), {
                transaction_id: selectedTx.id,
                phone,
                pin
            });

            alert("Purchase successful!");
            setShowModal(false);
            setPhone("");
            setPin("");
        } catch (err) {
            alert("Failed. Try again.");
        }
    };

    return (
        <div>
            {/* 🔍 SEARCH */}
            <input
                type="text"
                placeholder="Search product plan (e.g 1GB MTN)"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="border p-2 mb-4 w-full"
            />

            {/* 📊 TABLE */}
            <table className="w-full border">
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {filteredItems.map(tx => (
                        <tr key={tx.id}>
                            <td>{tx.product_plan}</td>
                            <td>{tx.amount}</td>
                            <td>
                                <button
                                    onClick={() => handleBuyAgain(tx)}
                                    className="bg-blue-500 text-white px-3 py-1 rounded"
                                >
                                    Buy Again
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>

            {loading && <p className="text-center mt-4">Loading...</p>}

            {/* 🧾 MODAL */}
            {showModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                    <div className="bg-white p-6 rounded w-96">
                        <h2 className="text-lg font-bold mb-3">
                            Buy {selectedTx?.product_plan}
                        </h2>

                        <input
                            type="text"
                            placeholder="Phone Number"
                            value={phone}
                            onChange={(e) => setPhone(e.target.value)}
                            className="border p-2 w-full mb-3"
                        />

                        <input
                            type="password"
                            placeholder="PIN"
                            value={pin}
                            onChange={(e) => setPin(e.target.value)}
                            className="border p-2 w-full mb-3"
                        />

                        <div className="flex justify-between">
                            <button
                                onClick={() => setShowModal(false)}
                                className="bg-gray-400 text-white px-3 py-1 rounded"
                            >
                                Cancel
                            </button>

                            <button
                                onClick={handlePurchase}
                                className="bg-green-600 text-white px-3 py-1 rounded"
                            >
                                Confirm Purchase
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}