import BtnMsg from "../../fns/btnMsg"

const ku = (
	Pw: React.RefObject<HTMLInputElement>,
	Pw2: React.RefObject<HTMLInputElement>,
	ms: React.RefObject<HTMLParagraphElement>,
	iB: React.RefObject<HTMLInputElement>,
) => {
	BtnMsg(ms, iB) // show btn, hide message

	// remove empty spaces
	Pw.current && (Pw.current.value = Pw.current.value.replace(/\s/g, ""))
	Pw2.current && (Pw2.current.value = Pw2.current.value.replace(/\s/g, ""))
}

export default ku
