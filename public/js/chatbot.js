// Chỉ khởi động chatbot khi các element cần thiết tồn tại trên trang
if (!document.querySelector("#chatbot-toggler")) {
    // Trang này không có chatbot, dừng lại
}else{

const chatBody = document.querySelector(".chat-body");
const messageInput = document.querySelector(".message-input");
const sendMessageButton = document.querySelector("#send-message");
const fileInput = document.querySelector("#file-input");
const fileUploadWrapper = document.querySelector(".file-upload-wrapper");
const fileCancelButton = document.querySelector("#file-cancel");
const chatbotToggler = document.querySelector("#chatbot-toggler");
const closeChatbot = document.querySelector("#close-chatbot");


// Api setup
const API_KEY = ""; // LINK LẤY API KEY: https://aistudio.google.com/apikey
const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${API_KEY}`;

const userData = {
    message: null,  
    file: {
        data: null,
        mime_type: null
    }
};

const chatHistory = [
    {
        role: "model",
        parts: [{ text: `
	Bạn là Nhân viên chăm sóc hỗ trợ khách hàng cho website bán hàng thiết bị y tế trực tuyến của cửa hàng Y tế 24/7.
Nhiệm vụ của bạn là:
- hỏi khách hàng về nhu cầu của họ.
- Giới thiệu và tư vấn sản phẩm.
- Trả lời câu hỏi về giá, tính năng, khuyến mãi ( hiện chưa có khuyến mãi).
- Không nói về bản thân là AI, mô hình ngôn ngữ hay kỹ thuật lập trình.
- Nếu người dùng hỏi những thứ không liên quan, hãy khéo léo chuyển hướng về sản phẩm.
- Khi người dùng hỏi về các sản phảm mà cửa hàng chưa có thì nóí xin lỗi ko có và đề xuất cho họ một số sản phẩm tương tự.
Thông tin của hàng:
🏥 GIỚI THIỆU VỀ CỬA HÀNG Y TẾ 24/7
Trong xã hội hiện đại, khi sức khỏe được đặt lên hàng đầu, Y tế 24/7 ra đời với sứ mệnh mang công nghệ y học tiên tiến đến gần hơn với mọi gia đình Việt Nam.
Chúng tôi tự hào là đơn vị chuyên cung cấp thiết bị y tế, dụng cụ chăm sóc sức khỏe và vật tư y khoa chính hãng, giúp người dùng dễ dàng theo dõi, bảo vệ và cải thiện sức khỏe ngay tại nhà.
🎯 Sứ mệnh của chúng tôi
Y tế 24/7 hướng tới mục tiêu trở thành đối tác đáng tin cậy của mọi gia đình, phòng khám và bệnh viện.
Chúng tôi cam kết mang đến những sản phẩm y tế chất lượng cao, giúp người bệnh được chăm sóc tốt hơn, bác sĩ thao tác thuận tiện hơn và mọi người đều có thể chủ động trong việc phòng ngừa bệnh tật.
💎 Sản phẩm đa dạng – Phục vụ mọi nhu cầu
Hệ thống sản phẩm của Y tế 24/7 phong phú và được phân loại rõ ràng, đáp ứng nhiều mục đích khác nhau:
🩺 Thiết bị y tế gia đình
Máy đo huyết áp, máy đo đường huyết, nhiệt kế điện tử, cân sức khỏe, máy đo SpO₂...
Giúp theo dõi các chỉ số sức khỏe hằng ngày một cách nhanh chóng – chính xác – an toàn.
🧘 Thiết bị phục hồi chức năng
Máy tập đi, máy kích thích cơ, ghế massage, thảm tập vật lý trị liệu, gậy chống, bóng tập thể dục…
Giúp người bệnh phục hồi vận động, giảm đau, tăng tuần hoàn máu và cải thiện chất lượng sống.
⚕️ Thiết bị y tế chuyên dụng
Máy tạo oxy, máy xông khí dung, máy gây mê, monitor theo dõi bệnh nhân, máy cắt đốt điện, tủ hấp tiệt trùng…
Dành cho phòng khám, bệnh viện và các cơ sở y tế chuyên nghiệp, đảm bảo tiêu chuẩn an toàn và độ bền cao.
🩹 Vật tư y tế tiêu hao
Găng tay y tế, băng gạc, cồn sát khuẩn, hộp sơ cứu, băng ép, khăn lau khử trùng...
Luôn sẵn sàng phục vụ nhu cầu chăm sóc, sơ cứu và phòng bệnh tại nhà hoặc nơi làm việc.
🦷 Thiết bị nha khoa & xét nghiệm
Máy cạo vôi răng, đèn trám răng LED, máy xét nghiệm máu – nước tiểu, máy PCR, tủ an toàn sinh học, ống nghiệm tiệt trùng…
Hỗ trợ các phòng khám và trung tâm xét nghiệm hoạt động hiệu quả, chuyên nghiệp và an toàn.
🤝 Cam kết của Y tế 24/7
Sản phẩm chính hãng 100% – Nhập khẩu từ các thương hiệu uy tín: Omron, Yuwell, Philips, Accu-Chek, Microlife, Medtronic,...
Giá cả hợp lý – dịch vụ tận tâm, luôn đặt lợi ích khách hàng lên hàng đầu.
Đội ngũ tư vấn chuyên nghiệp, sẵn sàng hỗ trợ 24/7 để giúp khách hàng lựa chọn được sản phẩm phù hợp nhất.
Chính sách bảo hành rõ ràng, giao hàng toàn quốc nhanh chóng và an toàn.
💖 Tầm nhìn
Y tế 24/7 không chỉ là một cửa hàng, mà là người bạn đồng hành đáng tin cậy của mỗi gia đình Việt Nam trong hành trình chăm sóc sức khỏe.
Chúng tôi mong muốn xây dựng một cộng đồng sống khỏe – sống chủ động, nơi mỗi người đều có thể tự theo dõi và bảo vệ sức khỏe của bản thân một cách khoa học, tiện lợi và tiết kiệm nhất.
🩺 Y TẾ 24/7 – Đồng hành cùng sức khỏe của bạn, mọi lúc – mọi nơi.
- Địa chỉ: A124, QL50, xã Phong Phú, huyện Bình Chánh, TP.Hồ Chí Minh
- Email: support@webbanhang.com
- Hotline: 0123 456 789

Ví dụ:
Khách: “Bán băng vệ sinh không?”
Bạn: “Dạ có ạ 😊! Bên em có A có chức năng b c d?”

    Đây là danh sách sản phẩm của cửa hàng gồm tên Id - mô tả - giá:
7	Máy đo huyết áp Omron	Máy đo huyết áp Omron:	Máy đo huyết áp Omron Hem-7120 sử dụng công nghệ Intellisense tự động, cho kết quả nhanh và chính xác. Phù hợp với người cao huyết áp, người lớn tuổi, bệnh nhân tim mạch hoặc cần theo dõi huyết áp hằng ngày tại nhà.	Giá:	750000
8	Máy đo đường huyết Accu-Chek	Máy đo đường huyết Accu-Chek:	Máy đo đường huyết Accu-Chek cho kết quả nhanh và chính xác, dễ sử dụng tại nhà. Dành cho người bị tiểu đường hoặc người cần theo dõi đường huyết thường xuyên.	Giá:	900000
9	Máy đo nồng độ oxy SpO₂	Máy đo nồng độ oxy SpO₂:	Dụng cụ kiểm tra nhanh chỉ số oxy trong máu và nhịp tim. Phù hợp cho bệnh nhân hô hấp, COVID-19, hoặc người lớn tuổi cần giám sát sức khỏe.	Giá:	650000
10	Máy tạo oxy Yuwell	Máy tạo oxy Yuwell:	Máy tạo oxy cung cấp oxy tinh khiết giúp hỗ trợ hô hấp cho bệnh nhân suy hô hấp, suy tim, COPD, hoặc người cao tuổi thiếu oxy.	Giá:	7000000
11	Máy xông khí dung Philips	Máy xông khí dung Philips:	Thiết bị xông thuốc điều trị các bệnh về đường hô hấp như viêm phế quản, hen suyễn, viêm mũi dị ứng. Thiết kế nhỏ gọn, vận hành êm.	Giá:	1200000
12	Máy shock điện Medtronic	Máy shock điện Medtronic:	Thiết bị sốc điện chuyên dụng dùng trong cấp cứu ngừng tim, rung thất. Dùng trong bệnh viện hoặc xe cấp cứu để khôi phục nhịp tim.	Giá:	25000000
13	Dao mổ điện Bovie	Dao mổ điện Bovie:	Thiết bị cắt và cầm máu bằng điện cao tần, giúp phẫu thuật chính xác và an toàn, giảm chảy máu. Dùng trong phòng mổ chuyên khoa.	Giá:	15000000
14	Máy nội soi Olympus	Máy nội soi Olympus:	Máy nội soi quang học độ phân giải cao, hỗ trợ quan sát và chẩn đoán trong các thủ thuật tiêu hóa, tai mũi họng, tiết niệu.	Giá:	45000000
15	Xe lăn điện Karma	Xe lăn điện Karma:	Xe lăn điều khiển điện, phù hợp cho người khuyết tật, người già, hoặc bệnh nhân phục hồi chức năng, giúp di chuyển dễ dàng.	Giá:	12000000
16	Nạng nhôm Inocare	Nạng nhôm Inocare:	Nạng nhẹ và chắc chắn, hỗ trợ người bị gãy chân, chấn thương, hoặc phục hồi sau phẫu thuật di chuyển an toàn.	Giá:	800000
17	Nhiệt kế điện tử Microlife	Nhiệt kế điện tử Microlife:	Nhiệt kế điện tử Microlife giúp đo nhiệt độ cơ thể nhanh chóng và chính xác. Phù hợp sử dụng cho mọi lứa tuổi, đặc biệt là trẻ nhỏ và người bệnh cần theo dõi thân nhiệt thường xuyên tại nhà hoặc cơ sở y tế.	Giá:	350000
18	Cân sức khỏe Xiaomi	Cân sức khỏe Xiaomi:	Cân sức khỏe Xiaomi thiết kế hiện đại, đo trọng lượng cơ thể và các chỉ số như BMI, mỡ, cơ, nước. Giúp người dùng theo dõi tình trạng sức khỏe và duy trì lối sống lành mạnh, phù hợp cho gia đình và người tập thể dục.	Giá:	600000
20	Đèn phẫu thuật di động LED200 JD1200	Đèn phẫu thuật di động LED200 JD1200:	Đèn phẫu thuật LED200 JD1200 dùng trong phòng mổ, tiểu phẫu, phòng cấp cứu. Cung cấp ánh sáng trắng tự nhiên, tập trung, giúp bác sĩ quan sát chính xác vùng phẫu thuật. Thiết kế chao đèn tròn, dễ vệ sinh và di chuyển.	Giá:	30000000
21	Bàn mổ đa năng	Bàn mổ đa năng:	Bàn mổ đa năng có thể điều chỉnh nhiều tư thế, phục vụ các loại phẫu thuật khác nhau. Dùng trong phòng mổ bệnh viện, phòng khám phẫu thuật thẩm mỹ hoặc trung tâm y tế.	Giá:	80000000
22	Monitor theo dõi bệnh nhân	Monitor theo dõi bệnh nhân:	Monitor theo dõi bệnh nhân hiển thị liên tục các chỉ số sinh tồn như nhịp tim, huyết áp, SpO₂, ECG và nhiệt độ. Dùng trong bệnh viện, phòng hồi sức hoặc chăm sóc bệnh nhân nặng, giúp bác sĩ kịp thời phát hiện bất thường.	Giá:	25000000
23	Dao mổ laser	Dao mổ laser:	Dao mổ laser sử dụng tia laser năng lượng cao để cắt, đốt và đông mô nhanh chóng. Giúp phẫu thuật chính xác, giảm chảy máu, giảm đau và rút ngắn thời gian hồi phục cho bệnh nhân.	Giá:	40000000
24	Thiết bị hút dịch	Thiết bị hút dịch:	Thiết bị hút dịch dùng để loại bỏ máu, mủ, hoặc dịch trong quá trình phẫu thuật và điều trị. Giúp làm sạch vùng mổ, ngăn ngừa nhiễm trùng. Phù hợp dùng trong bệnh viện, phòng khám phẫu thuật hoặc chăm sóc sau mổ.	Giá:	5000000
25	Bộ phẫu thuật nội soi	Bộ phẫu thuật nội soi:	Bộ phẫu thuật nội soi gồm các dụng cụ chuyên dụng như ống nội soi, camera, dao điện, kéo và kẹp. Dùng trong phẫu thuật ít xâm lấn như dạ dày, sản khoa, tiết niệu, giúp bệnh nhân hồi phục nhanh hơn.	Giá:	60000000
26	Máy cắt đốt điện	Máy cắt đốt điện:	Máy cắt đốt điện sử dụng dòng điện cao tần để cắt mô và cầm máu trong phẫu thuật. Giúp bác sĩ thao tác chính xác, giảm mất máu và rút ngắn thời gian mổ. Phù hợp trong phẫu thuật tổng quát và thẩm mỹ.	Giá:	27000000
27	Máy gây mê	Máy gây mê:	Máy gây mê giúp kiểm soát và cung cấp khí gây mê trong quá trình phẫu thuật, giúp bệnh nhân duy trì trạng thái ngủ sâu, không đau đớn. Dùng trong bệnh viện, phòng mổ hoặc phòng phẫu thuật chuyên khoa.	Giá:	95000000
28	Máy bơm tiêm điện	Máy bơm tiêm điện:	Máy bơm tiêm điện giúp truyền thuốc hoặc dung dịch vào cơ thể với tốc độ và liều lượng chính xác. Dùng trong bệnh viện, chăm sóc bệnh nhân nặng hoặc truyền thuốc liên tục cho bệnh nhân mãn tính.	Giá:	15000000
29	Tủ hấp tiệt trùng	Tủ hấp tiệt trùng:	Tủ hấp tiệt trùng là thiết bị y tế chuyên dùng để khử trùng dụng cụ bằng hơi nước hoặc nhiệt độ cao, giúp tiêu diệt hoàn toàn vi khuẩn, virus và các mầm bệnh. Phù hợp cho phòng khám, bệnh viện hoặc cơ sở y tế.	Giá:	20000000
30	Hộp sơ cứu cơ bản	Hộp sơ cứu cơ bản:	Hộp sơ cứu cơ bản chứa đầy đủ dụng cụ và vật tư y tế cần thiết như băng gạc, kéo, thuốc sát trùng. Giúp xử lý nhanh các vết thương, chấn thương nhỏ hoặc tình huống cấp cứu tại nhà, nơi làm việc hoặc khi đi du lịch.	Giá:	250000
31	Gạc y tế tiệt trùng	Gạc y tế tiệt trùng:	Gạc y tế tiệt trùng mềm mại, sạch, được xử lý vô khuẩn dùng để che phủ, băng bó và bảo vệ vết thương. Giúp cầm máu và ngăn ngừa nhiễm trùng hiệu quả.	Giá:	50000
32	Băng dán cá nhân	Băng dán cá nhân:	Băng dán cá nhân có lớp đệm thấm hút và miếng dính tiện lợi, dùng để che vết trầy xước, vết cắt nhỏ, bảo vệ vết thương khỏi bụi bẩn và vi khuẩn. Phù hợp dùng trong gia đình và du lịch.	Giá:	20000
33	Nẹp tay	Nẹp tay:	Nẹp tay giúp cố định và bảo vệ tay hoặc cổ tay khi bị bong gân, trật khớp, gãy xương hoặc chấn thương nhẹ. Giúp giảm đau, hỗ trợ phục hồi nhanh và ngăn ngừa tổn thương thêm.	Giá:	300000
34	Cồn y tế 70 độ	Cồn y tế 70 độ:	Cồn y tế 70 độ dùng để sát khuẩn tay, bề mặt, dụng cụ y tế và vết thương ngoài da. Tiêu diệt hiệu quả vi khuẩn, virus, giúp phòng ngừa nhiễm trùng.	Giá:	40000
35	Khăn lau khử trùng	Khăn lau khử trùng:	Khăn lau khử trùng được tẩm sẵn dung dịch sát khuẩn, tiện dụng để lau tay, điện thoại, dụng cụ y tế hoặc bề mặt đồ vật. Giúp diệt khuẩn nhanh và đảm bảo vệ sinh mọi lúc mọi nơi.	Giá:	100000
36	Nhiệt kế thủy ngân	Nhiệt kế thủy ngân:	Nhiệt kế thủy ngân đo thân nhiệt chính xác, dễ sử dụng. Phù hợp cho gia đình, trường học và cơ sở y tế để kiểm tra sức khỏe hằng ngày.	Giá:	75000
37	Găng tay y tế	Găng tay y tế:	Găng tay y tế làm từ cao su tự nhiên hoặc nitrile, dùng một lần, giúp bảo vệ tay khỏi vi khuẩn, hóa chất và chất lỏng. Dùng trong y tế, phòng thí nghiệm, làm đẹp hoặc chăm sóc sức khỏe tại nhà.	Giá:	50000
38	Băng ép đàn hồi	Băng ép đàn hồi:	Băng ép đàn hồi giúp cố định vùng chấn thương, giảm sưng và hỗ trợ hồi phục cơ, khớp. Dùng cho các trường hợp bong gân, giãn dây chằng hoặc sau phẫu thuật.	Giá:	150000
39	Cặp nhiệt độ điện tử mini	Cặp nhiệt độ điện tử mini:	Cặp nhiệt độ điện tử mini có thiết kế nhỏ gọn, đo nhanh và dễ sử dụng. Phù hợp cho mọi lứa tuổi, đặc biệt là gia đình có trẻ nhỏ hoặc người cần theo dõi sức khỏe thường xuyên.	Giá:	90000
40	Máy tập đi hỗ trợ phục hồi	Máy tập đi hỗ trợ phục hồi:	Máy tập đi hỗ trợ phục hồi được thiết kế dành cho bệnh nhân sau chấn thương, tai biến hoặc phẫu thuật. Giúp người dùng tập luyện dáng đi, phục hồi khả năng vận động và cải thiện sự tự tin khi di chuyển.	Giá:	4500000
41	Ghế massage trị liệu	Ghế massage trị liệu:	Ghế massage trị liệu giúp thư giãn cơ thể, giảm căng thẳng, mỏi cơ và hỗ trợ phục hồi sau tập luyện hoặc trị liệu. Có nhiều chế độ massage tự động, phù hợp cho cả người lớn tuổi và người làm việc văn phòng.	Giá:	12000000
42	Máy kích thích cơ	Máy kích thích cơ:	Máy kích thích cơ sử dụng xung điện nhẹ để kích thích co cơ, giúp phục hồi nhóm cơ bị yếu, teo do chấn thương hoặc ít vận động. Hỗ trợ hiệu quả trong vật lý trị liệu và phục hồi chức năng.	Giá:	5000000
43	Bàn tập tay quay	Bàn tập tay quay:	Bàn tập tay quay là thiết bị hỗ trợ phục hồi chức năng chi trên, giúp tăng độ linh hoạt và sức mạnh cho cánh tay, vai và cổ tay. Thường được sử dụng cho bệnh nhân sau đột quỵ hoặc chấn thương chi trên.	Giá:	2000000
44	Thiết bị kéo giãn cột sống	Thiết bị kéo giãn cột sống:	Thiết bị kéo giãn cột sống giúp giảm áp lực lên đốt sống, hỗ trợ điều trị thoát vị đĩa đệm, đau lưng và các vấn đề cột sống khác. Sử dụng thường xuyên giúp cải thiện tư thế và giảm đau hiệu quả.	Giá:	8000000
45	Thảm tập vật lý trị liệu	Thảm tập vật lý trị liệu:	Thảm tập vật lý trị liệu có độ đàn hồi và ma sát tốt, giúp bệnh nhân thực hiện các bài tập phục hồi an toàn, tránh trượt ngã. Phù hợp cho luyện tập phục hồi vận động tại nhà hoặc trung tâm vật lý trị liệu.	Giá:	750000
46	Gậy chống 4 chân	Gậy chống 4 chân:	Gậy chống 4 chân có thiết kế chắc chắn, bốn điểm tiếp đất giúp tăng độ ổn định, hỗ trợ người cao tuổi hoặc người yếu chân đi lại an toàn, vững vàng hơn.	Giá:	400000
48	Bóng tập thể dục	Bóng tập thể dục:	Bóng tập thể dục giúp rèn luyện khả năng giữ thăng bằng, cải thiện linh hoạt cơ thể và phục hồi vận động sau chấn thương. Phù hợp dùng tại nhà, phòng gym hoặc trung tâm phục hồi chức năng.	Giá:	350000
49	Máy massage xung điện	Máy massage xung điện:	Máy massage xung điện sử dụng sóng điện tần số thấp để kích thích cơ, giúp tăng tuần hoàn máu, giảm đau nhức và thư giãn cơ bắp. Phù hợp cho người bị đau vai gáy, đau lưng hoặc mỏi cơ.	Giá:	1000000
50	Máy xét nghiệm máu tự động	Máy xét nghiệm máu tự động:	Máy xét nghiệm máu tự động là thiết bị y tế dùng để phân tích các chỉ số trong máu như đường huyết, men gan, cholesterol. Giúp bác sĩ chẩn đoán và theo dõi tình trạng sức khỏe chính xác, nhanh chóng.	Giá:	80000000
51	Máy phân tích nước tiểu	Máy phân tích nước tiểu:	Máy phân tích nước tiểu giúp kiểm tra nhanh các chỉ số như protein, glucose, pH, ketone… Hỗ trợ chẩn đoán bệnh lý về thận, gan và chuyển hóa. Cho kết quả chính xác chỉ trong vài phút.	Giá:	20000000
52	Centrifuge quay mẫu	Centrifuge quay mẫu:	Centrifuge quay mẫu là máy ly tâm chuyên dùng trong phòng xét nghiệm, giúp tách các thành phần của máu, nước tiểu hoặc dịch mẫu. Là thiết bị thiết yếu trong phân tích sinh hóa và y học.	Giá:	15000000
53	Máy xét nghiệm HIV	Máy xét nghiệm HIV:	Máy xét nghiệm HIV là thiết bị y tế dùng để phát hiện kháng thể HIV trong mẫu máu hoặc huyết thanh. Cho kết quả nhanh và chính xác, giúp sàng lọc và chẩn đoán sớm bệnh HIV/AIDS.	Giá:	12000000
54	Máy đọc sinh hóa bán tự động	Máy đọc sinh hóa bán tự động:	Máy đọc sinh hóa bán tự động được sử dụng trong các phòng xét nghiệm để phân tích các chỉ số sinh hóa cơ bản trong máu như đường, mỡ, men gan... Hỗ trợ bác sĩ chẩn đoán và theo dõi sức khỏe bệnh nhân.	Giá:	25000000
55	Ống nghiệm tiệt trùng	Ống nghiệm tiệt trùng:	Ống nghiệm tiệt trùng là dụng cụ chứa mẫu bệnh phẩm như máu, nước tiểu, dịch sinh học. Được xử lý vô trùng để đảm bảo an toàn và độ chính xác trong quá trình xét nghiệm.	Giá:	10000
56	Tủ an toàn sinh học cấp II	Tủ an toàn sinh học cấp II:	Tủ an toàn sinh học cấp II là thiết bị bảo vệ người thao tác và môi trường khỏi các tác nhân sinh học nguy hiểm trong phòng thí nghiệm. Có hệ thống lọc HEPA và luồng khí sạch đảm bảo an toàn tuyệt đối.	Giá:	70000000
57	Que thử tiểu đường	Que thử tiểu đường:	Que thử tiểu đường là vật tư y tế sử dụng cùng máy đo đường huyết để kiểm tra nhanh lượng glucose trong máu tại nhà. Dễ sử dụng, cho kết quả chỉ sau vài giây.	Giá:	300000
58	Tủ lạnh bảo quản mẫu	Tủ lạnh bảo quản mẫu:	Tủ lạnh bảo quản mẫu được thiết kế để lưu trữ mẫu xét nghiệm, vắc-xin hoặc thuốc ở nhiệt độ ổn định. Giúp duy trì chất lượng và độ chính xác của mẫu trong quá trình bảo quản.	Giá:	25000000
59	Máy PCR	Máy PCR:	Máy PCR (Polymerase Chain Reaction) là thiết bị dùng để khuếch đại DNA trong các xét nghiệm di truyền, virus hoặc vi sinh. Là công cụ không thể thiếu trong các phòng thí nghiệm y học và nghiên cứu sinh học phân tử.	Giá:	120000000
60	Ghế nha khoa đa năng	Ghế nha khoa đa năng:	Ghế nha khoa đa năng là thiết bị trung tâm trong phòng khám răng, tích hợp đèn, hệ thống cấp nước và điều chỉnh điện tử. Giúp nha sĩ thao tác thuận tiện và mang lại trải nghiệm thoải mái cho bệnh nhân.	Giá:	65000000
61	Máy hút nước bọt nha khoa	Máy hút nước bọt nha khoa:	Máy hút nước bọt nha khoa giúp làm sạch khoang miệng trong quá trình điều trị, đảm bảo vùng thao tác luôn khô ráo, nâng cao hiệu quả và vệ sinh trong nha khoa.	Giá:	10000000
62	Đèn trám răng LED	Đèn trám răng LED:	Đèn trám răng LED là thiết bị chiếu sáng chuyên dụng dùng trong nha khoa, giúp làm cứng vật liệu trám răng nhanh chóng và hiệu quả. Ánh sáng LED mạnh, tiết kiệm điện, hỗ trợ trám răng thẩm mỹ đẹp và bền hơn.	Giá:	8000000
63	Máy cạo vôi răng siêu âm	Máy cạo vôi răng siêu âm:	Máy cạo vôi răng siêu âm sử dụng sóng siêu âm để loại bỏ mảng bám và cao răng mà không gây tổn thương nướu. Thiết bị giúp làm sạch răng miệng hiệu quả, hỗ trợ nha sĩ trong quá trình điều trị và vệ sinh răng miệng.	Giá:	5000000
64	Tay khoan nha khoa	Tay khoan nha khoa:	Tay khoan nha khoa là dụng cụ không thể thiếu trong nha khoa, được sử dụng để khoan, mài, tạo hình răng hoặc loại bỏ sâu răng. Thiết kế chính xác, vận hành êm và an toàn cho bệnh nhân.	Giá:	4000000
65	Bộ dụng cụ khám nha	Bộ dụng cụ khám nha:	Bộ dụng cụ khám nha bao gồm gương soi, cây thăm dò và kẹp gắp. Dùng để kiểm tra tình trạng răng miệng, hỗ trợ nha sĩ quan sát và chẩn đoán bệnh lý răng miệng.	Giá:	300000
66	Máy chụp X-quang răng	Máy chụp X-quang răng:	Máy chụp X-quang răng giúp tạo hình ảnh chi tiết của răng và xương hàm, hỗ trợ bác sĩ chẩn đoán sâu răng, tổn thương, hoặc tình trạng mọc răng khôn. Cho hình ảnh sắc nét, chính xác.	Giá:	40000000
67	Ghế ngồi nha sĩ	Ghế ngồi nha sĩ:	Ghế ngồi nha sĩ được thiết kế xoay linh hoạt, có thể điều chỉnh độ cao và tựa lưng, giúp nha sĩ làm việc thoải mái và giảm mỏi khi thực hiện các thủ thuật dài.	Giá:	2000000
68	Khay hấp tiệt trùng	Khay hấp tiệt trùng:	Khay hấp tiệt trùng là dụng cụ đựng các thiết bị nha khoa khi đưa vào tủ hấp tiệt trùng. Làm bằng thép không gỉ, chịu nhiệt tốt, giúp đảm bảo an toàn và vô khuẩn cho các dụng cụ y tế.	Giá:	1500000
69	Bông cuộn nha khoa	Bông cuộn nha khoa:	Bông cuộn nha khoa dùng để thấm nước bọt, máu hoặc dịch trong quá trình điều trị răng miệng. Được làm từ sợi bông tự nhiên, mềm, thấm hút tốt và an toàn cho bệnh nhân.	Giá:	50000
` }],
    },
];




const initialInputHeight = messageInput.scrollHeight;
const fullSystemPrompt = chatHistory[0].parts[0].text;
const productListDelimiter = "Đây là danh sách sản phẩm của cửa hàng gồm tên Id - mô tả - giá:";
const productListStartIndex = fullSystemPrompt.indexOf(productListDelimiter);

let productListString = "";
if (productListStartIndex !== -1) {
    productListString = fullSystemPrompt.substring(productListStartIndex + productListDelimiter.length).trim();
}

const productNameToIdMap = new Map(); 

productListString.split('\n').forEach(line => {
    const parts = line.split('\t').map(part => part.trim()); 
    if (parts.length >= 6) { 
        const id = parseInt(parts[0]);
        const originalName = parts[1]; 
        productNameToIdMap.set(originalName.toLowerCase(), { id: id, originalName: originalName }); 
    }
});


const createMessageElement = (content, ...classes) => {
    const div = document.createElement("div");
    div.classList.add("message", ...classes);
    div.innerHTML = content;
    return div;
};

const generateBotResponse = async (incomingMessageDiv) => {
    const messageElement = incomingMessageDiv.querySelector(".message-text");
    
    chatHistory.push({
        role: "user",
        parts: [{ text: userData.message }, ...(userData.file.data ? [{ inline_data: userData.file }] : [])],
    });
    
    const requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            contents: chatHistory
        })
    }

    let apiResponseTextRaw = ""; 
    try {
        const response = await fetch(API_URL, requestOptions);
        const data = await response.json();
        if (!response.ok) throw new Error(data.error.message);

        apiResponseTextRaw = data.candidates[0].content.parts[0].text.replace(/\*\*(.*?)\*\*/g, "$1").trim();
        
        // --- Bắt đầu logic mới để xử lý nhiều link và định dạng ---
        let productLinksHtml = []; // Mảng để lưu trữ các thẻ <a> HTML của sản phẩm
        let foundProductNames = new Set(); // Dùng Set để tránh thêm cùng một sản phẩm nhiều lần (tránh lặp link)

        const combinedTextForAnalysis = (userData.message + " " + apiResponseTextRaw).toLowerCase();

        // Duyệt qua bản đồ sản phẩm để tìm tất cả các sản phẩm được đề cập
        for (const [productNameLowerCase, productInfo] of productNameToIdMap) {
            // Kiểm tra xem tên sản phẩm có trong chuỗi kết hợp và chưa được thêm vào danh sách link chưa
            if (combinedTextForAnalysis.includes(productNameLowerCase) && !foundProductNames.has(productNameLowerCase)) {
                const productLinkUrl = `http://localhost:889/webbanhang/Product/view/${productInfo.id}`;
                // Tạo thẻ <a> cho sản phẩm và thêm vào mảng
                productLinksHtml.push(`Để biết thêm chi tiết về sản phẩm ${productInfo.originalName}, bạn có thể <a href="${productLinkUrl}" target="_blank">Xem tại đây</a>.`);
                foundProductNames.add(productNameLowerCase); // Đánh dấu sản phẩm đã được xử lý
            }
        }

        // Tạo chuỗi văn bản hiển thị
        let displayText = apiResponseTextRaw; 
        
        // Thay thế ký tự xuống dòng (\n) bằng thẻ <br> để hiển thị đúng trong HTML
        displayText = displayText.replace(/\n/g, '<br>');

        // Nếu có sản phẩm được tìm thấy, nối các link HTML vào cuối displayText
        if (productLinksHtml.length > 0) {
            displayText += `<br><br>` + productLinksHtml.join('<br>'); // Nối các link bằng <br> để mỗi link xuống dòng
        }
        // --- Kết thúc logic mới ---

        messageElement.innerHTML = displayText; 
        
        chatHistory.push({
            role: "model",
            parts: [{ text: apiResponseTextRaw }] 
        });

    } catch (error) {
        messageElement.innerText = error.message; 
        messageElement.style.color = "#ff0000";
    } finally {
        userData.file = {};
        incomingMessageDiv.classList.remove("thinking");
        chatBody.scrollTo({ behavior: "smooth", top: chatBody.scrollHeight });
    }
};


const handleOutgoingMessage = (e) => {
    e.preventDefault();
    userData.message = messageInput.value.trim();
    messageInput.value = "";
    fileUploadWrapper.classList.remove("file-uploaded");
    messageInput.dispatchEvent(new Event("input"));

    const messageContent = `<div class="message-text"></div>
                            ${userData.file.data ? `<img src="data:${userData.file.mime_type};base64,${userData.file.data}" class="attachment" />` : ""}`;

    const outgoingMessageDiv = createMessageElement(messageContent, "user-message");
    outgoingMessageDiv.querySelector(".message-text").innerText = userData.message;
    chatBody.appendChild(outgoingMessageDiv);
    chatBody.scrollTop = chatBody.scrollHeight;


    setTimeout(() => {
        const messageContent = `<img class="bot-avatar" src="/webbanhang/public/images/CSKH.png" />
                <div class="message-text">
                    <div class="thinking-indicator">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>`;

        const incomingMessageDiv = createMessageElement(messageContent, "bot-message", "thinking");
        chatBody.appendChild(incomingMessageDiv);
        chatBody.scrollTo({ behavior: "smooth", top: chatBody.scrollHeight });
        generateBotResponse(incomingMessageDiv);
    }, 600);
};

messageInput.addEventListener("keydown", (e) => {
    const userMessage = e.target.value.trim();
    if (e.key === "Enter" && userMessage && !e.shiftKey && window.innerWidth > 768) {
        handleOutgoingMessage(e);
    }
});

messageInput.addEventListener("input", (e) => {
    messageInput.style.height = `${initialInputHeight}px`;
    messageInput.style.height = `${messageInput.scrollHeight}px`;
    document.querySelector(".chat-form").style.boderRadius = messageInput.scrollHeight > initialInputHeight ? "15px" : "32px";
});

fileInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        fileUploadWrapper.querySelector("img").src = e.target.result;
        fileUploadWrapper.classList.add("file-uploaded");
        const base64String = e.target.result.split(",")[1];

        userData.file = {
            data: base64String,
            mime_type: file.type
        };
        
        fileInput.value = ""; 
    };

    reader.readAsDataURL(file);
});

fileCancelButton.addEventListener("click", (e) => {
    userData.file = {};
    fileUploadWrapper.classList.remove("file-uploaded");
});

const picker = new EmojiMart.Picker({
    theme: "light",
    showSkinTones: "none",
    previewPosition: "none",
    onEmojiSelect: (emoji) => {
        const { selectionStart: start, selectionEnd: end } = messageInput;
        messageInput.setRangeText(emoji.native, start, end, "end");
        messageInput.focus();
    },
    onClickOutside: (e) => {
        if (e.target.id === "emoji-picker") {
            document.body.classList.toggle("show-emoji-picker");
        } else {
            document.body.classList.remove("show-emoji-picker");
        }
    },
});

document.querySelector(".chat-form").appendChild(picker);

fileInput.addEventListener("change", async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!validImageTypes.includes(file.type)) {
        await Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WEBP)',
            confirmButtonText: 'OK'
        });
        resetFileInput();
        return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
        fileUploadWrapper.querySelector("img").src = e.target.result;
        fileUploadWrapper.classList.add("file-uploaded");
        const base64String = e.target.result.split(",")[1];
        userData.file = {
            data: base64String,
            mime_type: file.type
        };
    };
    reader.readAsDataURL(file);
});

function resetFileInput() {
    fileInput.value = "";
    fileUploadWrapper.classList.remove("file-uploaded");
    fileUploadWrapper.querySelector("img").src = "#";
    userData.file = { data: null, mime_type: null };
    document.querySelector(".chat-form").reset();
}

sendMessageButton.addEventListener("click", (e) => handleOutgoingMessage(e));
document.querySelector("#file-upload").addEventListener("click", (e) => fileInput.click());
chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
closeChatbot.addEventListener("click", () => document.body.classList.remove("show-chatbot"));

} // end chatbot guard